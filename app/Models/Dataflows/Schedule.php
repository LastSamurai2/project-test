<?php

namespace App\Models\Dataflows;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    const STATUS_DISABLED = 'disabled';
    const STATUS_ENABLED = 'enabled';

    protected $table = 'alekseon_dataflow_schedule';

    protected $profile;
    protected $statusLabel;
    protected $result;
    protected $executionType;
    protected $paramterValues;

    /**
     * @var array
     */
    protected $outputModels = [];

    /**
     * @return false
     */
    public function usesTimestamps()
    {
        return false;
    }

    /**
     * @param $outputModel
     * @return $this
     */
    public function addOutputModel($outputModel)
    {
        $this->outputModels[] = $outputModel;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOutputModels()
    {
        return $this->outputModels;
    }

    /**
     *
     */
    public function execute()
    {
        $runner = new Runner();
        $runner->executeSchedule($this);
    }

    /**
     * @return mixed
     */
    public function getProfile()
    {
        if ($this->profile == null) {
            if ($this->profile_class) {
                $this->profile = new $this->profile_class();
                if (!($this->profile instanceof ProfileInterface)) {
                    throw new \Exception(
                        'Please define a correct profile instance.'
                    );
                }
                $this->profile->setSchedule($this);
            }
        }
        return $this->profile;
    }

    /**
     * @param $result
     * @return $this
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return mixed
     */
    public function getStatusLabel()
    {
        return $this->statusLabel;
    }

    /**
     * @param $statusLabel
     * @return $this
     */
    public function setStatusLabel($statusLabel)
    {
        $this->statusLabel = $statusLabel;
        return $this;
    }

    /**
     * @param $executionType
     * @return $this
     */
    public function setExecutionType($executionType)
    {
        $this->executionType = $executionType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExecutionType()
    {
        return $this->executionType;
    }

    /**
     * @return false
     */
    public function getSemaphores()
    {
        //@todo
        return false;
    }

    /**
     * @throws \Exception
     */
    public function initParameters()
    {
        $parametersFormConfig = $this->getProfile()->getParametersFormConfig();

        $this->setAttribute('parameters_config',
            $parametersFormConfig
        );

        $parameters = $this->getParameters();
        $this->setAttribute('parameters', $parameters);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getParameters()
    {
        if ($this->paramterValues == null) {
            $this->paramterValues = [];
            $parametersFormConfig = $this->getProfile()->getParametersFormConfig();
            $scheduleParameters = json_decode($this->parameters, true);
            foreach ($parametersFormConfig as $code => $config) {
                $this->paramterValues[$code] =  isset($scheduleParameters[$code]) ? $scheduleParameters[$code] : '';
            }
        }
        return $this->paramterValues;
    }
}
