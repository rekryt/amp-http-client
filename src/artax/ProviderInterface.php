<?php

/**
 * Artax ProviderInterface Interface File
 * 
 * PHP version 5.4
 * 
 * @category artax
 * @package  core
 * @author   Daniel Lowrey <rdlowrey@gmail.com>
 */

namespace artax {
  
  /**
   * ProviderInterface
   * 
   * @category artax
   * @package  core
   * @author   Daniel Lowrey <rdlowrey@gmail.com>
   */
  interface ProviderInterface
  {
    /**
     * Factory method for object instantiation
     * 
     * @param string $type   A DotNotation string class name
     * @param array  $custom A key/value array specifying custom dependency objects
     */
    public function make($type, array $custom);
  }
}
