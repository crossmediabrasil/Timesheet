<?php

/**
 * LessPHP Minified v0.1
 * Extended 2011-07-17 by Jeff Hughes, www.jeffhughes.ca
 * Notes for extension can be found here: http://disjointedthinking.jeffhughes.ca/2011/07/lessphp-minified/
 *
 * Extended from lessphp v0.2.1
 * http://leafo.net/lessphp
 *
 * LESS CSS compiler, adapted from http://lesscss.org/docs.html
 *
 * Copyright 2010, Leaf Corcoran <leafot@gmail.com>
 * Licensed under MIT or GPLv3, see LICENSE
 */

define('MINIFY_ELEM_LINE', true); // remove new lines between elements
define('MINIFY_ELEM_LIST', true); // remove spaces in lists of elements (e.g., 'html, body, a {' becomes 'html,body,a{' )
define('MINIFY_PROP_LINE', true); // remove new lines between properties
define('MINIFY_LAST_SEMI', true); // removes last semi-colon in each list of properties
define('MINIFY_HEX', true); // shorten hex codes when possible (e.g., '#ffffff' becomes '#fff')
define('MINIFY_UNITS', true); // removes units for 0 values (e.g. '0px' becomes '0'), rounds decimal values to 2 places, and removes leading zeroes before decimal points

if (!defined('ALWAYS_COMPILE')) {
    define('ALWAYS_COMPILE', false); // if set to 'true', will always compile a new CSS file -- useful for development, not recommended for production
}

require 'lessc.inc.php';

class LessMinified extends lessc {

  // compile to $in to $out if $in is newer than $out
  // returns true when it compiles, false otherwise
  public static function ccompile($in, $out) {
    if(ALWAYS_COMPILE || (!is_file($out) || filemtime($in) > filemtime($out))){
      $less = new LessMinified($in);
      file_put_contents($out, trim($less->parse()));
      return true;
    }

    return false;
  }

  /**
   * Recursively compiles a block. 
   * @param $block the block
   * @param $parentTags the tags of the block that contained this one
   *
   * A block is analogous to a CSS block in most cases. A single less document
   * is encapsulated in a block when parsed, but it does not have parent tags
   * so all of it's children appear on the root level when compiled.
   *
   * Blocks are made up of props and children.
   *
   * Props are property instructions, array tuples which describe an action
   * to be taken, eg. write a property, set a variable, mixin a block.
   *
   * The children of a block are just all the blocks that are defined within.
   *
   * Compiling the block involves pushing a fresh environment on the stack,
   * and iterating through the props, compiling each one.
   *
   * See lessc::compileProp()
   *
   */
  function compileBlock($block, $parent_tags = null) {
    $isRoot = $parent_tags == null && $block->tags == null;

    $indent = str_repeat($this->indentChar, $this->indentLevel);

    if (!empty($block->no_multiply)) {
      $special_block = true;
      $this->indentLevel++;
      $tags = array();
    } else {
      $special_block = false;
      $tags = $this->multiplyTags($parent_tags, $block->tags);
    }

    $this->pushEnv();
    $lines = array();
    $blocks = array();
    foreach ($block->props as $prop) {
      $this->compileProp($prop, $block, $tags, $lines, $blocks);
    }

    $this->pop();

    $nl = $isRoot ? "\n".$indent :
      "\n".$indent.$this->indentChar;

    ob_start();

    if ($special_block) {
      $this->indentLevel--;
      if (isset($block->media)) {
        list($media_types, $media_rest) = $block->media;
        echo "@media ".join(', ', $media_types).
          (!empty($media_rest) ? " $media_rest" : '' );
      } elseif (isset($block->keyframes)) {
        echo $block->tags[0]." ".
          $this->compileValue($this->reduce($block->keyframes));
      } else {
        list($name) = $block->tags;
        echo $indent.$name;
      }
      
      // ADDED: below
      if(MINIFY_ELEM_LIST)
        echo '{'."\n";
      else
        echo ' {'.(count($lines) > 0 ? $nl : "\n");
    }

    // dump it
    if (count($lines) > 0) {
      if (!$special_block && !$isRoot) {
        // ADDED: below
        if(MINIFY_ELEM_LIST)
          echo implode(",", $tags);
        else
          echo $indent.implode(", ", $tags).' ';
        if(MINIFY_PROP_LINE){
          echo "{";
        } else {
          if (count($lines) > 1) echo "{".$nl;
          else echo "{ ";
        }
      }
      
      // ADDED: below
      if(MINIFY_LAST_SEMI)
        $lines[(count($lines)-1)] = substr($lines[(count($lines)-1)], 0, -1); // remove semi-colon
      if(MINIFY_PROP_LINE)
        echo implode($lines);
      else
        echo implode($nl, $lines);

      if (!$special_block && !$isRoot) {
        // ADDED: below
        if(MINIFY_PROP_LINE){
          echo "}";
        } else {
          if (count($lines) > 1) echo "\n".$indent."}";
          else echo " }";
        }
        if(!MINIFY_ELEM_LINE)
          echo "\n";
      } else echo "\n";
    }

    foreach ($blocks as $b) echo $b;

    if ($special_block) {
      echo $indent."}\n";
    }

    return ob_get_clean();
  }
  
  // compile a prop and update $lines or $blocks appropriately
  function compileProp($prop, $block, $tags, &$_lines, &$_blocks) {
    switch ($prop[0]) {
    case 'assign':
      list(, $name, $value) = $prop;
      if ($name[0] == $this->vPrefix) {
        $this->set($name, $this->reduce($value));
      } else {
        $_lines[] = "$name:".
          $this->compileValue($this->reduce($value)).";";
      }
      break;
    case 'block':
      list(, $child) = $prop;
      $_blocks[] = $this->compileBlock($child, $tags);
      break;
    case 'mixin':
      list(, $path, $args) = $prop;

      $mixin = $this->findBlock($block, $path);
      if (is_null($mixin)) {
        // echo "failed to find block: ".implode(" > ", $path)."\n";
        break; // throw error here??
      }

      $have_args = false;
      if (isset($mixin->args)) {
        $have_args = true;
        $this->pushEnv();
        $this->zipSetArgs($mixin->args, $args);
      }

      list($name) = $mixin->tags;
      if ($name == "div") {
        print_r($mixin->props);
      }

      $old_parent = $mixin->parent;
      $mixin->parent = $block;

      foreach ($mixin->props as $sub_prop) {
        $this->compileProp($sub_prop, $mixin, $tags, $_lines, $_blocks);
      }

      $mixin->parent = $old_parent;

      if ($have_args) $this->pop();

      break;
    case 'raw':
      $_lines[] = $prop[1];
      break;
    case 'import':
      list(, $path) = $prop;
      $this->addParsedFile($path);
      $root = $this->createChild($path)->parseTree();

      $root->parent = $block;
      foreach ($root->props as $sub_prop) {
        $this->compileProp($sub_prop, $root, $tags, $_lines, $_blocks);
      }

      // inject imported blocks into this block, local will overwrite import
      $block->children = array_merge($root->children, $block->children);
      break;
    case 'charset':
      list(, $value) = $prop;
      $_lines[] = '@charset '.$this->compileValue($this->reduce($value)).';';
      break;
    default:
      echo "unknown op: {$prop[0]}\n";
      throw new exception();
    }
  }
  
  /**
   * Compiles a primitive value into a CSS property value.
   *
   * Values in lessphp are typed by being wrapped in arrays, their format is
   * typically:
   *
   *     array(type, contents [, additional_contents]*)
   *
   * Will not work on non reduced values (expressions, variables, etc)
   */
  function compileValue($value) {
    switch ($value[0]) {
    case 'list':
      // [1] - delimiter
      // [2] - array of values
      return implode($value[1], array_map(array($this, 'compileValue'), $value[2]));
    case 'keyword':
      // [1] - the keyword 
    case 'number':
      // [1] - the number 
      return $value[1];
    case 'string':
      // [1] - contents of string (includes quotes)
      
      // search for inline variables to replace
      $replace = array();
      if (preg_match_all('/{('.$this->preg_quote($this->vPrefix).'[\w-_][0-9\w-_]*?)}/', $value[1], $m)) {
        foreach ($m[1] as $name) {
          if (!isset($replace[$name]))
            $replace[$name] = $this->compileValue($this->reduce(array('variable', $name)));
        }
      }
      foreach ($replace as $var=>$val) {
        // strip quotes
        if (preg_match('/^(["\']).*?(\1)$/', $val)) {
          $val = substr($val, 1, -1);
        }
        $value[1] = str_replace('{'.$var.'}', $val, $value[1]);
      }

      return $value[1];
    case 'color':
      // [1] - red component (either number for a %)
      // [2] - green component
      // [3] - blue component
      // [4] - optional alpha component
      if (count($value) == 5) { // rgba
        return 'rgba('.$value[1].','.$value[2].','.$value[3].','.$value[4].')';
      }
      
      // ADDED: below
      if(MINIFY_HEX){
        $countsame = 0;
        foreach(range(1,3) as $i){
          $piece[$i] = ($value[$i] < 16 ? '0' : '').dechex($value[$i]);
          if($piece[$i]{0} == $piece[$i]{1})
            $countsame++;
        }
        if($countsame == 3){ // if all three RGB hex values can be shortened, then shorten
          $out = '#'.$piece[1]{0}.$piece[2]{0}.$piece[3]{0};
        } else {
          $out = '#'.implode($piece);
        }
      } else {
        $out = sprintf("#%02x%02x%02x", $value[1], $value[2], $value[3]);
      }
      return $out;
    case 'function':
      // [1] - function name
      // [2] - some value representing arguments

      // see if function evaluates to something else
      $value = $this->reduce($value);
      if ($value[0] == 'function') {
        return $value[1].'('.$this->compileValue($value[2]).')';
      }
      else return $this->compileValue($value);
    default: // assumed to be unit  
      // ADDED: below
      if(MINIFY_UNITS){
        if($value[1] == 0){
          return $value[1]; // return unitless if value is 0
        } else {
          if(is_float($value[1]*1)){
            $newval = round($value[1], 2);
            if($newval < 1){
              $newval2 = ltrim(abs($newval), '0'); // remove leading zero before decimal point
              $newval = ($newval < 0) ? '-'.$newval2 : $newval2;
            }
            return $newval.$value[0];
          } else {
            return $value[1].$value[0];
          }
        }
      } else {
        return $value[1].$value[0];
      }
    }
  }

}

?>