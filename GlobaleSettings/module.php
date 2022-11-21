<?php

declare(strict_types=1);
require_once __DIR__ . '/../libs/helper/VariableProfileHelper.php';

    class GlobaleSettings extends IPSModule
    {
        use VariableProfileHelper;

        public function Create()
        {
            //Never delete this line!
            parent::Create();
            $this->ConnectParent('{C6D2AEB3-6E1F-4B2E-8E69-3A1A00246850}');
            $this->RegisterPropertyString('topic', 'openWB');

            $this->RegisterProfileIntegerEx('OWBMQTT.Lademodus', 'Power', '', '', [
                [0, $this->translate('Immediately'),  '', -1],
                [1, $this->translate('Min+PV'),  '', -1],
                [2, $this->translate('Only PV'),  '', -1],
                [3, $this->translate('Stop'),  '', -1],
                [4, $this->translate('Standby'),  '', -1]
            ]);

            $this->RegisterProfileInteger('OWB.Ladeleistung', 'Electricity', '', ' A', 6, 32, 1);

            $this->RegisterVariableInteger('GlobalChargeMode', $this->Translate('Charge Mode'), 'OWBMQTT.Lademodus', 0);
            $this->EnableAction('GlobalChargeMode');
            $this->RegisterVariableInteger('minEVSECurrentAllowed', $this->Translate('Min EVSE Current Allowed'), 'OWBMQTT.Lademodus', 0);
            $this->EnableAction('minEVSECurrentAllowed');
            $this->RegisterVariableInteger('minCurrentMinPV', $this->Translate('Min Current PVMin'), 'OWB.Ladeleistung', 0);
            $this->EnableAction('minCurrentMinPV');
            $this->RegisterVariableString('SimulateRFID', $this->Translate('Simulate RFID'), '', 0);
            $this->EnableAction('SimulateRFID');

             
        }

        public function Destroy()
        {
            //Never delete this line!
            parent::Destroy();
        }

        public function ApplyChanges()
        {
            //Never delete this line!
            parent::ApplyChanges();
            $this->ConnectParent('{C6D2AEB3-6E1F-4B2E-8E69-3A1A00246850}');

            //Setze Filter für ReceiveData
            $MQTTTopic = $this->ReadPropertyString('topic');
            $this->SetReceiveDataFilter('.*' . $MQTTTopic . '.*');
        }

        public function ReceiveData($JSONString)
        {
            if (!empty($this->ReadPropertyString('topic'))) {
                $this->SendDebug('ReceiveData :: JSON', $JSONString, 0);
                $data = json_decode($JSONString, true);
                switch ($data['Topic']) {
                    case $this->ReadPropertyString('topic') . '/global/ChargeMode':
                        $this->SetValue('GlobalChargeMode', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/config/get/global/minEVSECurrentAllowed':
                        $this->SetValue('minEVSECurrentAllowed', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/config/get/pv/minCurrentMinPv':
                        $this->SetValue('minCurrentMinPv', $data['Payload']);
                        break;
                    default:
                        break;
                }
            }
        }

        public function RequestAction($Ident, $Value)
        {
            switch ($Ident) {
                case 'GlobalChargeMode':
                    $this->MQTTCommand('set/ChargeMode', $Value);
                    break;
                case 'minEVSECurrentAllowed':
                    $this->MQTTCommand('config/set/global/minEVSECurrentAllowed', $Value);
                    break;                    
                case 'minCurrentMinPV':
                    $this->MQTTCommand('config/set/pv/minCurrentMinPv', $Value);
                    break;
                case 'SimulateRFID':
                    $this->MQTTCommand('set/system/SimulateRFID', $Value);
                    break;
                case 'priorityModeEVBattery':
                    $this->MQTTCommand('config/set/pv/priorityModeEVBattery', $Value);
                    break;
                    
                default:
                    $this->LogMessage('Invalid Action', KL_WARNING);
                    break;
            }
        }

        private function MQTTCommand($Topic, $Payload, $retain = 0)
        {
            $Topic = $this->ReadPropertyString('topic') . '/' . $Topic;
            $Data['DataID'] = '{043EA491-0325-4ADD-8FC2-A30C8EEB4D3F}';
            $Data['PacketType'] = 3;
            $Data['QualityOfService'] = 0;
            $Data['Retain'] = boolval($retain);
            $Data['Topic'] = $Topic;
            $Data['Payload'] = strval($Payload);
            $JSON = json_encode($Data, JSON_UNESCAPED_SLASHES);
            $result = @$this->SendDataToParent($JSON);

            if ($result === false) {
                $last_error = error_get_last();
                echo $last_error['message'];
            }
        }
    }
