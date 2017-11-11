<?

class Youless extends IPSModule
{

    public function Create()
    {
        //Never delete this line!
        parent::Create();

        //These lines are parsed on Symcon Startup or Instance creation
        //You cannot use variables here. Just static values.
        $this->RegisterPropertyString("Host", "");
        $this->RegisterPropertyInteger("UpdateInterval", "15");
        $this->RegisterTimer('YoulessTimerUpdate', 15000, 'Youless_Update('.$this->InstanceID.');');
        $this->RegisterTimer('YoulessStatusUpdate', 5000, 'Youless_GetState('.$this->InstanceID.');');
    }

    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();
        $this->RegisterVariableFloat("YoulessCounterReading", $this->Translate("counter reading"), "~Power", 1);
        $this->RegisterProfileInteger("Youless.Watt", "Electricity", "", " Watt", 0, 0, 0, 0);
        $this->RegisterVariableInteger("YoulessCurrentPower", $this->Translate("current power"), "Youless.Watt", 2);
        $this->RegisterVariableInteger("YoulessSignalStrength", $this->Translate("signal strength"), "~Intensity.100", 3);
        $this->RegisterVariableBoolean("YoulessCounterState", $this->Translate("counter state"), "~Switch", 4);


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
        $ip = $this->ReadPropertyString("Host");
        $url = "http://" . $ip . "/a?f=j";
        if ($state) {
            $handle = fopen($url, "r");
            $json = fgets($handle, 10000);
            fclose($handle);
            $Meter = json_decode($json);
            $this->SendDebug("Youless LS120:", "Data: ".$json,0);
            SetValue($this->GetIDForIdent("YoulessCounterReading"), floatval(str_replace(",", ".", $Meter->cnt)));
            SetValue($this->GetIDForIdent("YoulessCurrentPower"), intval($Meter->pwr));
            SetValue($this->GetIDForIdent("YoulessSignalStrength"), intval($Meter->lvl));
            return $Meter;
        }
        return false;
    }

    public function GetState()
    {
        $state = false;
        $ip = $this->ReadPropertyString("Host");
        if (!$ip == "")
        {
            $state = Sys_Ping($ip, 1000);
            SetValueBoolean($this->GetIDForIdent("YoulessCounterState") , $state);
        }
        return $state;
    }

    protected function SetUpdateIntervall()
    {
        $interval = ($this->ReadPropertyInteger("UpdateInterval"))*1000; // interval ms
        $this->SetTimerInterval("YoulessTimerUpdate", $interval);
    }

    protected function RegisterProfileInteger($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $StepSize, $Digits)
    {

        if(!IPS_VariableProfileExists($Name)) {
            IPS_CreateVariableProfile($Name, 1);
        } else {
            $profile = IPS_GetVariableProfile($Name);
            if($profile['ProfileType'] != 1)
                throw new Exception("Variable profile type does not match for profile ".$Name);
        }

        IPS_SetVariableProfileIcon($Name, $Icon);
        IPS_SetVariableProfileText($Name, $Prefix, $Suffix);
        IPS_SetVariableProfileDigits($Name, $Digits); //  Nachkommastellen
        IPS_SetVariableProfileValues($Name, $MinValue, $MaxValue, $StepSize); // string $ProfilName, float $Minimalwert, float $Maximalwert, float $Schrittweite

    }

    protected function RegisterProfileIntegerAss($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $Stepsize, $Digits, $Associations)
    {
        if ( sizeof($Associations) === 0 ){
            $MinValue = 0;
            $MaxValue = 0;
        }
        /*
        else {
            //undefiened offset
            $MinValue = $Associations[0][0];
            $MaxValue = $Associations[sizeof($Associations)-1][0];
        }
        */
        $this->RegisterProfileInteger($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $Stepsize, $Digits);

        //boolean IPS_SetVariableProfileAssociation ( string $ProfilName, float $Wert, string $Name, string $Icon, integer $Farbe )
        foreach($Associations as $Association) {
            IPS_SetVariableProfileAssociation($Name, $Association[0], $Association[1], $Association[2], $Association[3]);
        }

    }


    protected function RegisterProfileFloat($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $StepSize, $Digits)
    {

        if(!IPS_VariableProfileExists($Name)) {
            IPS_CreateVariableProfile($Name, 2);
        } else {
            $profile = IPS_GetVariableProfile($Name);
            if($profile['ProfileType'] != 2)
                throw new Exception("Variable profile type does not match for profile ".$Name);
        }

        IPS_SetVariableProfileIcon($Name, $Icon);
        IPS_SetVariableProfileText($Name, $Prefix, $Suffix);
        IPS_SetVariableProfileDigits($Name, $Digits); //  Nachkommastellen
        IPS_SetVariableProfileValues($Name, $MinValue, $MaxValue, $StepSize);

    }

    protected function RegisterProfileFloatAss($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $Stepsize, $Digits, $Associations)
    {
        if ( sizeof($Associations) === 0 ){
            $MinValue = 0;
            $MaxValue = 0;
        }
        /*
        else {
        //undefiened offset
        $MinValue = $Associations[0][0];
        $MaxValue = $Associations[sizeof($Associations)-1][0];
        }
        */
        $this->RegisterProfileFloat($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $Stepsize, $Digits);

        //boolean IPS_SetVariableProfileAssociation ( string $ProfilName, float $Wert, string $Name, string $Icon, integer $Farbe )
        foreach($Associations as $Association) {
            IPS_SetVariableProfileAssociation($Name, $Association[0], $Association[1], $Association[2], $Association[3]);
        }

    }

    //Configuration Form
    public function GetConfigurationForm()
    {
        $formhead = $this->FormHead();
        $formactions = $this->FormActions();
        $formelementsend = '{ "type": "Label", "label": "__________________________________________________________________________________________________" }';
        $formstatus = $this->FormStatus();
        return	'{ '.$formhead.$formelementsend.'],'.$formactions.$formstatus.' }';
    }


    protected function FormHead()
    {
        $form = '"elements":
            [
                { "name": "Host",                 "type": "ValidationTextBox", "caption": "IP-Address" },
                { "type": "Label", "label": "Update Interval Youless" },
                { "type": "IntervalBox", "name": "UpdateInterval", "caption": "seconds" },
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

}