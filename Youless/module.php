<?php
declare(strict_types=1);

require_once __DIR__ . '/../libs/ProfileHelper.php';
require_once __DIR__ . '/../libs/ConstHelper.php';

class Youless extends IPSModule
{
    use ProfileHelper;

    public function Create()
    {
        //Never delete this line!
        parent::Create();

        //These lines are parsed on Symcon Startup or Instance creation
        //You cannot use variables here. Just static values.
        $this->RegisterPropertyString('Host', '');
        $this->RegisterPropertyInteger('UpdateInterval', 15);
        $this->RegisterTimer('YoulessTimerUpdate', 15000, 'Youless_Update(' . $this->InstanceID . ');');
        $this->RegisterTimer('YoulessStatusUpdate', 5000, 'Youless_GetState(' . $this->InstanceID . ');');
        $this->RegisterPropertyBoolean('show_S0', false);
    }

    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();
        $this->RegisterVariableFloat('YoulessCounterReading', $this->Translate('counter reading'), '~Power', 1);
        $this->RegisterProfile('Youless.Watt', 'Electricity', '', ' Watt', 0, 127, 1, 0, VARIABLETYPE_INTEGER);
        $this->RegisterVariableInteger('YoulessCurrentPower', $this->Translate('current power'), 'Youless.Watt', 2);
        $this->RegisterVariableInteger('YoulessSignalStrength', $this->Translate('signal strength'), '~Intensity.100', 3);
        $this->RegisterVariableBoolean('YoulessCounterState', $this->Translate('counter state'), '~Switch', 4);
        $show_S0 = $this->ReadPropertyBoolean('show_S0');
        if($show_S0)
        {
            $this->RegisterVariableInteger('YoulessS0Power', $this->Translate('S0 power'), 'Youless.Watt', 5);
        }
        else
        {
            $this->UnregisterVariable('YoulessS0Power');
        }

        $this->ValidateConfiguration();
    }

    private function ValidateConfiguration()
    {
        $host = $this->ReadPropertyString('Host');

        //IP  prüfen
        if (!filter_var($host, FILTER_VALIDATE_IP) === false)
        {
            //IP ok
            $this->SetUpdateIntervall();
            // Status Aktiv
            $this->SetStatus(102);
        }
        else
        {
            $this->SetStatus(203); //IP Adresse ist ungültig
        }
    }

    public function Update()
    {
        $state = $this->GetState();
        $ip = $this->ReadPropertyString('Host');
        $url = 'http://' . $ip . '/a?f=j';
        if ($state) {
            $handle = fopen($url, 'r');
            $json = fgets($handle, 10000);
            fclose($handle);
            $Meter = json_decode($json);
            $this->SendDebug('Youless LS120:', 'Data: ' . $json, 0);
            $cnt = 0;
            if(isset($Meter->cnt))
			{
				$cnt = floatval(str_replace(',', '.', $Meter->cnt));
			}
			if(isset($Meter->pwr))
			{
				$pwr = intval($Meter->pwr);
				SetValue($this->GetIDForIdent('YoulessCurrentPower'), $pwr);
			}
			if(isset($Meter->lvl))
			{
				$lvl = intval($Meter->lvl);
				SetValue($this->GetIDForIdent('YoulessSignalStrength'), $lvl);
			}
			if(isset($Meter->dev))
			{
				$dev = intval($Meter->dev);
				$this->SendDebug('Youless LS120:', 'dev: ' . $dev, 0);
			}
			if(isset($Meter->det))
			{
				$det = intval($Meter->det);
				$this->SendDebug('Youless LS120:', 'det: ' . $det, 0);
			}
			if(isset($Meter->con))
			{
				$con = intval($Meter->con);
				$this->SendDebug('Youless LS120:', 'con: ' . $con, 0);
			}
			if(isset($Meter->sts))
			{
				$sts = intval($Meter->sts);
				$this->SendDebug('Youless LS120:', 'sts: ' . $sts, 0);
			}
			$cs0 = 0;
			if(isset($Meter->cs0))
			{
				$cs0 = floatval(str_replace(',', '.', $Meter->cs0));
				$this->SendDebug('Youless LS120:', 'cs0: ' . $cs0, 0);
			}
            if($cnt == 0 && $cs0 > 0)
			{
				$counterreading = $cs0;
			}
            else
			{
				$counterreading = $cnt;
			}
			SetValue($this->GetIDForIdent('YoulessCounterReading'), $counterreading);

			$show_S0 = $this->ReadPropertyBoolean('show_S0');
            if($show_S0 && (isset($Meter->ps0)))
            {
                SetValue($this->GetIDForIdent('YoulessS0Power'), intval($Meter->ps0));
            }
			if(isset($Meter->raw))
			{
				$raw = intval($Meter->raw);
				$this->SendDebug('Youless LS120:', 'raw: ' . $raw, 0);
			}

            return $Meter;
        }
        return false;
    }

    public function GetState()
    {
        $state = false;
        $ip = $this->ReadPropertyString('Host');
        if (!$ip == '')
        {
            $state = Sys_Ping($ip, 1000);
            SetValueBoolean($this->GetIDForIdent('YoulessCounterState'), $state);
        }
        return $state;
    }

    protected function SetUpdateIntervall()
    {
        $interval = ($this->ReadPropertyInteger('UpdateInterval'))*1000; // interval ms
        $this->SendDebug('Youless LS120:', 'Set update interval to ' . $interval/1000 . 'seconds', 0);
        $this->SetTimerInterval('YoulessTimerUpdate', $interval);
    }

    /**
     * build configuration form
     * @return string
     */
    public function GetConfigurationForm()
    {
        // return current form
        $Form = json_encode([
            'elements' => $this->FormHead(),
            'actions' => $this->FormActions(),
            'status' => $this->FormStatus()
        ]);
        $this->SendDebug('FORM', $Form, 0);
        $this->SendDebug('FORM', json_last_error_msg(), 0);
        return $Form;
    }

    /**
     * return form configurations on configuration step
     * @return array
     */
    protected function FormHead()
    {
        $form = [
            [
                'name' => 'Host',
                'type' => 'ValidationTextBox',
                'visible' => true,
                'caption' => 'IP-Address'
            ],
            [
                'name' => 'UpdateInterval',
                'type' => 'NumberSpinner',
                'visible' => true,
                'caption' => 'Update Interval Youless',
                'suffix' => 'seconds',
                'min' => 10
            ],
            [
                'name' => 'show_S0',
                'type' => 'CheckBox',
                'visible' => true,
                'caption' => 'S0 data'
            ]
        ];
        return $form;
    }

    /**
     * return form actions by token
     * @return array
     */
    protected function FormActions()
    {
        $form = [
            [
                'type' => 'Button',
                'visible' => true,
                'caption' => 'Update',
                'onClick' => 'Youless_Update($id);'
            ]
        ];
        return $form;
    }

    /**
     * return from status
     * @return array
     */
    protected function FormStatus()
    {
        $form = [
            [
                'code' => IS_CREATING,
                'icon' => 'inactive',
                'caption' => 'Creating instance.'
            ],
            [
                'code' => IS_ACTIVE,
                'icon' => 'active',
                'caption' => 'configuration valid.'
            ],
            [
                'code' => IS_INACTIVE,
                'icon' => 'inactive',
                'caption' => 'interface closed.'
            ],
            [
                'code' => 201,
                'icon' => 'inactive',
                'caption' => 'Please follow the instructions.'
            ],
            [
                'code' => 203,
                'icon' => 'error',
                'caption' => 'ip address is not valid.'
            ]
        ];

        return $form;
    }
}
