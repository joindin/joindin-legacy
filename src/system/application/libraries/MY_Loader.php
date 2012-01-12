<?php

class MY_Loader extends CI_Loader 
{
    
    private function getModelPath()
    {
        return APPPATH.'models';
    }
    
    public function model_obj($objectType, $id = null)
    {
        // See if there's a model that exists
        $modelName = ucwords(strtolower($objectType));
        $modelFile = $this->getModelPath().'/'.$modelName.'.php';
        if (is_file($modelFile))
        {
            include_once($modelFile);
            return new $modelName($id);
        } else {
            return null;
        }
    }
    
}

?>
