<?php

declare(strict_types=1);
require_once __DIR__ . '/../libs/helper/VariableProfileHelper.php';

    class EVU extends IPSModule
    {
        use VariableProfileHelper;

        public function Create()
        {
            //Never delete this line!
            parent::Create();
            $this->ConnectParent('{C6D2AEB3-6E1F-4B2E-8E69-3A1A00246850}');
            $this->RegisterPropertyString('topic', 'openWB');

            $this->RegisterProfileFloat('OWB.FloatAmpere', 'Electricity', '', ' A', 6, 32, 0.01, 2);
            $this->RegisterProfileInteger('OWB.IntegerAmpere', 'Electricity', '', ' A', 0, 0, 1);

            $this->RegisterVariableFloat('APhase1', $this->Translate('Phase 1'), 'OWB.FloatAmpere', 0);
            $this->RegisterVariableFloat('APhase2', $this->Translate('Phase 2'), 'OWB.FloatAmpere', 0);
            $this->RegisterVariableFloat('APhase3', $this->Translate('Phase 3'), 'OWB.FloatAmpere', 0);
            $this->RegisterVariableInteger('ASchieflast', $this->Translate('A Schieflast'), 'OWB.IntegerAmpere', 0);
            $this->RegisterVariableFloat('DailyYieldExportKwh', $this->Translate('Daily Yield Export'), '~Electricity', 0);
            $this->RegisterVariableFloat('DailyYieldImportKwh', $this->Translate('Daily Yield Import'), '~Electricity', 0);
            $this->RegisterVariableInteger('faultState', $this->Translate('Fault State'), '', 0);
            $this->RegisterVariableString('faultStr', $this->Translate('Fault State'), '', 0);
            $this->RegisterVariableFloat('Hz', $this->Translate('Frequency'), '~Hertz', 0);
            $this->RegisterVariableFloat('PfPhase1', $this->Translate('Pf Phase 1'), '', 0);
            $this->RegisterVariableFloat('PfPhase2', $this->Translate('Pf Phase 2'), '', 0);
            $this->RegisterVariableFloat('PfPhase3', $this->Translate('Pf Phase 3'), '', 0);
            $this->RegisterVariableFloat('W', $this->Translate('Power'), '~Watt', 0);
            $this->RegisterVariableFloat('kW', $this->Translate('Power in kw'), '~Power', 0);
            $this->RegisterVariableFloat('WhExported', $this->Translate('Wh Exported'), '~Electricity.Wh', 0);
            $this->RegisterVariableFloat('WhImported', $this->Translate('Wh Imported'), '~Electricity.Wh', 0);
            $this->RegisterVariableFloat('WhImported_temp', $this->Translate('Wh Imported Temp'), '', 0);
            $this->RegisterVariableFloat('WPhase1', $this->Translate('Power Phase 1'), '~Watt', 0);
            $this->RegisterVariableFloat('WPhase2', $this->Translate('Power Phase 2'), '~Watt', 0);
            $this->RegisterVariableFloat('WPhase3', $this->Translate('Power Phase 3'), '~Watt', 0);
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
                    case $this->ReadPropertyString('topic') . '/evu/APhase1':
                        $this->SetValue('APhase1', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/evu/APhase2':
                        $this->SetValue('APhase2', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/evu/APhase3':
                        $this->SetValue('APhase3', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/evu/ASchieflast':
                        $this->SetValue('ASchieflast', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/evu/DailyYieldExportKwh':
                        $this->SetValue('DailyYieldExportKwh', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/evu/DailyYieldImportKwh':
                        $this->SetValue('DailyYieldImportKwh', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/evu/faultState':
                        $this->SetValue('faultState', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/evu/faultStr':
                        $this->SetValue('faultStr', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/evu/Hz':
                        $this->SetValue('Hz', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/evu/PfPhase1':
                        $this->SetValue('PfPhase1', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/evu/PfPhase2':
                        $this->SetValue('PfPhase2', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/evu/PfPhase3':
                        $this->SetValue('PfPhase3', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/evu/W':
                        $this->SetValue('W', $data['Payload']);
                        $this->SetValue('kW', $data['Payload'] / 1000);
                        break;
                    case $this->ReadPropertyString('topic') . '/evu/WhExported':
                        $this->SetValue('WhExported', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/evu/WhImported':
                        $this->SetValue('WhImported', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/evu/WhImported_temp':
                        $this->SetValue('WhImported_temp', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/evu/WPhase1':
                        $this->SetValue('WPhase1', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/evu/WPhase2':
                        $this->SetValue('WPhase2', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/evu/WPhase3':
                        $this->SetValue('WPhase3', $data['Payload']);
                        break;
                    default:
                        break;
                }
            }
        }

        public function RequestAction($Ident, $Value)
        {
            switch ($Ident) {
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
