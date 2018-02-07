<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

namespace falcon\backend\app;


use yii\base\Component;

class Block extends Component
{

    /**
     * Generate id for using in JavaScript UI
     *
     * Function takes an arbitrary amount of parameters
     *
     * @param array ...$arguments
     *
     * @return string
     */
    public function getJsId(...$arguments)
    {
        $rawId = implode('-', $arguments);
        return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($rawId)), '-');
    }
}