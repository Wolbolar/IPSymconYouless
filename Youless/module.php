<?php

declare(strict_types=1);

class Youless extends IPSModule
{
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
            SetValue($this->GetIDForIdent('YoulessCounterReading'), floatval(str_replace(',', '.', $Meter->cnt)));
            SetValue($this->GetIDForIdent('YoulessCurrentPower'), intval($Meter->pwr));
            SetValue($this->GetIDForIdent('YoulessSignalStrength'), intval($Meter->lvl));
            $show_S0 = $this->ReadPropertyBoolean('show_S0');
            if($show_S0 && (isset($Meter->ps0)))
            {
                SetValue($this->GetIDForIdent('YoulessS0Power'), intval($Meter->ps0));
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
        $this->SetTimerInterval('YoulessTimerUpdate', $interval);
    }

    //Profile

    /**
     * register profiles.
     *
     * @param $Name
     * @param $Icon
     * @param $Prefix
     * @param $Suffix
     * @param $MinValue
     * @param $MaxValue
     * @param $StepSize
     * @param $Digits
     * @param $Vartype
     */
    protected function RegisterProfile($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $StepSize, $Digits, $Vartype)
    {

        if (!IPS_VariableProfileExists($Name)) {
            IPS_CreateVariableProfile($Name, $Vartype); // 0 boolean, 1 int, 2 float, 3 string,
        } else {
            $profile = IPS_GetVariableProfile($Name);
            if ($profile['ProfileType'] != $Vartype) {
                $this->SendDebug('profile', 'Variable profile type does not match for profile ' . $Name, 0);
            }
        }

        IPS_SetVariableProfileIcon($Name, $Icon);
        IPS_SetVariableProfileText($Name, $Prefix, $Suffix);
        if ($Vartype != VARIABLETYPE_STRING) {
            IPS_SetVariableProfileDigits($Name, $Digits); //  Nachkommastellen
            IPS_SetVariableProfileValues(
                $Name, $MinValue, $MaxValue, $StepSize
            ); // string $ProfilName, float $Minimalwert, float $Maximalwert, float $Schrittweite
        }
    }

    /**
     * register profile association.
     *
     * @param $Name
     * @param $Icon
     * @param $Prefix
     * @param $Suffix
     * @param $MinValue
     * @param $MaxValue
     * @param $Stepsize
     * @param $Digits
     * @param $Vartype
     * @param $Associations
     */
    protected function RegisterProfileAssociation($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $Stepsize, $Digits, $Vartype, $Associations)
    {
        if (is_array($Associations) && count($Associations) === 0) {
            $MinValue = 0;
            $MaxValue = 0;
        }
        $this->RegisterProfile($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $Stepsize, $Digits, $Vartype);

        if (is_array($Associations)) {
            foreach ($Associations as $Association) {
                IPS_SetVariableProfileAssociation($Name, $Association[0], $Association[1], $Association[2], $Association[3]);
            }
        } else {
            $Associations = $this->$Associations;
            foreach ($Associations as $code => $association) {
                IPS_SetVariableProfileAssociation($Name, $code, $this->Translate($association), $Icon, -1);
            }
        }

    }

    //Configuration Form
    public function GetConfigurationForm()
    {
        $formhead = $this->FormHead();
        $formactions = $this->FormActions();
        $formelementsend = '{ "type": "Label", "label": "__________________________________________________________________________________________________" }';
        $formstatus = $this->FormStatus();
        return	'{ ' . $formhead . $formelementsend . '],' . $formactions . $formstatus . ' }';
    }

    protected function FormHead()
    {
        $form = '"elements":
            [
                { "name": "Host",                 "type": "ValidationTextBox", "caption": "IP-Address" },
                { "type": "Label", "label": "Update Interval Youless" },
                { "type": "IntervalBox", "name": "UpdateInterval", "caption": "seconds" },
                { "type": "Label", "label": "Show S0 data" },
		        { "type": "CheckBox", "name": "show_S0",  "caption": "S0 data" },
                ';

        return $form;
    }

    protected function FormActions()
    {
        $form = '"actions":
			[
				{ "type": "Button", "label": "Update",  "onClick": "Youless_Update($id);" }
			],';
        return  $form;
    }

    protected function FormStatus()
    {
        $form = '"status":
            [
                {
                    "code": 101,
                    "icon": "inactive",
                    "caption": "Creating instance."
                },
				{
                    "code": 102,
                    "icon": "active",
                    "caption": "instance created."
                },
                {
                    "code": 104,
                    "icon": "inactive",
                    "caption": "interface closed."
                },
                {
                    "code": 203,
                    "icon": "error",
                    "caption": "ip address is not valid."
                }
            ]';
        return $form;
    }

    //Add this Polyfill for IP-Symcon 4.4 and older
    protected function SetValue($Ident, $Value)
    {

        if (IPS_GetKernelVersion() >= 5) {
            parent::SetValue($Ident, $Value);
        } else {
            SetValue($this->GetIDForIdent($Ident), $Value);
        }
    }
}
