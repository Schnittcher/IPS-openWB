<?php

declare(strict_types=1);
require_once __DIR__ . '/../libs/helper/VariableProfileHelper.php';

    class PVTotal extends IPSModule
    {
        use VariableProfileHelper;

        public function Create()
        {
            //Never delete this line!
            parent::Create();
            $this->ConnectParent('{C6D2AEB3-6E1F-4B2E-8E69-3A1A00246850}');
            $this->RegisterPropertyString('topic', 'openWB');

            $this->RegisterProfileIntegerEx('OWB.PriorityModeEVBattery', 'Power', '', '', [
                [0, $this->translate('Storage'),  '', -1],
                [1, $this->translate('Auto'),  '', -1]
            ]);

            $this->RegisterVariableBoolean('boolPVConfigured', $this->Translate('PV Configured'), '~Switch', 0);
            $this->RegisterVariableFloat('DailyYieldKwh', $this->Translate('Daily Yield'), '~Electricity', 0);
            $this->RegisterVariableInteger('faultState', $this->Translate('Fault State'), '', 0);
            $this->RegisterVariableString('faultStr', $this->Translate('Fault State'), '', 0);
            $this->RegisterVariableFloat('MonthlyYieldKwh', $this->Translate('Monthly Yield'), '~Electricity', 0);
            $this->RegisterVariableFloat('W', $this->Translate('Power'), '~Watt', 0);
            $this->RegisterVariableFloat('WhCounter', $this->Translate('Wh Counter'), '~Electricity.Wh', 0);
            $this->RegisterVariableFloat('YearlyYieldKwh', $this->Translate('Yearly Yield'), '~Electricity', 0);
            $this->RegisterVariableInteger('priorityModeEVBattery', $this->Translate('Priority ModeEV Battery'), 'OWB.PriorityModeEVBattery', 0);
            $this->EnableAction('priorityModeEVBattery');
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

                //Für MQTT Fix in IPS Version 6.3
                if (IPS_GetKernelDate() > 1670886000) {
                    $data['Payload'] = utf8_decode($data['Payload']);
                }

                switch ($data['Topic']) {
                    case $this->ReadPropertyString('topic') . '/pv/boolPVConfigured':
                        $this->SetValue('boolPVConfigured', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/pv/DailyYieldKwh':
                        $this->SetValue('DailyYieldKwh', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/pv/faultState':
                        $this->SetValue('faultState', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/pv/faultStr':
                        $this->SetValue('faultStr', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/pv/MonthlyYieldKwh':
                        $this->SetValue('MonthlyYieldKwh', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/pv/W':
                        $this->SetValue('W', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/pv/WhCounter':
                        $this->SetValue('WhCounter', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/pv/YearlyYieldKwh':
                        $this->SetValue('YearlyYieldKwh', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/pv/priorityModeEVBattery':
                        $this->SetValue('priorityModeEVBattery', $data['Payload']);
                        break;

                    default:
                        break;
                }
            }
        }

        public function RequestAction($Ident, $Value)
        {
            switch ($Ident) {
                case 'priorityModeEVBattery':

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
