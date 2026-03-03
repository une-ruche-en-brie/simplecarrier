<?php
namespace MondialrelayClasslib\Extensions;

abstract class AbstractModuleExtension
{
    //region Fields

    public $name;

    public $module;

    public $objectModels = [];

    public $hooks = [];

    public $extensionAdminControllers = [];

    public $controllers = [];

    public $cronTasks = []; //TODO

    //endregion

    /**
     * @param Module $module
     */
    public function setModule($module)
    {
        $this->module = $module;

        return $this;
    }
}