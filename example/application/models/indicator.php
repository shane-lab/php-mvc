<?php
class Indicator extends Model
{
    /**
     * @var int The id of this object
     */
    protected $_id;
    
    /**
     * @var string The indicator name
     */
    protected $_name;
    
    /**
     * @var Model The Phase Model object
     */
    protected $_phase;
    
    /**
     * @var string The indicator requirement
     */
    protected $_requirement;

    /**
     * @var integer The given score for this P/K/E Indicator
     */
    protected $_score;
} 