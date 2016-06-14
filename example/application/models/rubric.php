<?php
class Rubric extends Model
{
    /**
     * @var int The id of this object
     */
    protected $_id;

    /**
     * @var Model The Indicator Model object
     */
    protected $_indicator;

    /**
     * @var string The interests of this rubric
     */
    protected $_interest;

    /**
     * @var string The reflection of this rubric
     */
    protected $_reflection;

    /**
     * @var string The concrete examples of this rubric
     */
    protected $_examples;
} 