<?php
/**
 * Joind.in loader
 *
 * @category Joind.in
 * @package  Libraries
 * @license  http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * @link     http://github.com/joindin/joind.in
 */

/**
 * Joind.in loader
 *
 * @category Joind.in
 * @package  Libraries
 * @license  http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * @link     http://github.com/joindin/joind.in
 */
class MY_Loader extends CI_Loader
{
    /**
     * Returns the path to the models
     *
     * @return string
     */
    private function _getModelPath()
    {
        return APPPATH.'models';
    }

    /**
     * Loads the model object requested
     *
     * @param string $objectType Object type to load
     * @param null   $id         Not used
     *
     * @return mixed
     */
    public function model_obj($objectType, $id = null)
    {
        // See if there's a model that exists
        $modelName = ucwords(strtolower($objectType));
        $modelFile = $this->_getModelPath() . '/' . $modelName . '.php';
        if (is_file($modelFile)) {
            include_once $modelFile;
            return new $modelName($id);
        } else {
            return null;
        }
    }
    
}

