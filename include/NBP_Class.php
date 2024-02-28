<?php
Class NBP_Data{
    public $File_Path = 'http://api.nbp.pl/api/exchangerates/rates/c/';     //Bazowe podłączenie do API NBP do obsługi danych kursu walut według tabeli C
    public $XML_Data;                                   //Pobrane dane z API
    public $Currencies = Array('EUR', 'USD', 'CHF');    //Dostępne waluty do formularza wg standardu ISO 4217
    public $Request_Date;                               //Data z input
    public $Previous_Date;                              //Data poprzedzająca - w przypadku poniedziałku jest to piątek
    public $Request_Currency;                           //Waluta z input
    public $Currency_O;                                 //Obiekt waluty
    public function __construct(){
    }
    public function Set_REQUEST_Params(){
        if(isset($_REQUEST['Request_Date'])) $this->Request_Date = $_REQUEST['Request_Date'];
        else $this->Request_Date = date('Y-m-d');

        $this->Set_Previous_Date();

        if(isset($_REQUEST['Request_Currency'])) $this->Request_Currency = $_REQUEST['Request_Currency'];
    }
    public function Set_Previous_Date(){
        if(date('w', strtotime($this->Request_Date)) == 1) $Date_Change = 3;
        else $Date_Change = 1;

        $this->Previous_Date = date('Y-m-d', strtotime($this->Request_Date) - ($Date_Change* 86400));
    }
    public function Currency_Date_Form(){
        echo'<form method="post">';
            echo'<input type="date" name="Request_Date" value="'.$this->Request_Date.'" min="'.date('Y-m-d',strtotime(date('Y-m-d')) - (7 * 86400)).'" max="'.date('Y-m-d').'"></input>';
            echo' <select  name="Request_Currency">';
                foreach($this->Currencies as $Currency){
                    echo'<option value="'.$Currency.'"';
                        if($this->Request_Currency == $Currency) echo' selected ';
                    echo'>'.$Currency.'</option>';
                }
            echo'</select>';
            echo'<br><input type="submit" name="Get_Data" value="Pobierz Dane"></input>';
            echo'<hr>';
        echo'</form>';
    }
    public function Check_Date(){
        if(date('w', strtotime($this->Request_Date)) == 0 OR date('w', strtotime($this->Request_Date)) == 6){
            echo'<h1>Wybierz datę od poniedziałku do piątku.</h1>';
            return 0;
        }else return 1;
    }
    public function Get_Data(){
        $this->File_Path .= '/'.$this->Request_Currency;
        $this->File_Path .= '/'.$this->Previous_Date;
        $this->File_Path .= '/'.$this->Request_Date;
        $this->File_Path .= '/?format=xml';

        $this->XML_Data = file_get_contents($this->File_Path);
        $this->XML_Data = mb_convert_encoding($this->XML_Data, 'UTF-8', "ISO-8859-2");
        $this->XML_Data = simplexml_load_string($this->XML_Data);
    }
    public function Create_Currency_Object(){
        include_once 'include/Currency_Class.php';
        $this->Currency_O = new Currency($this);
        $this->Currency_O->Set_Data($this->XML_Data);
        $this->Currency_O->Check_Difference();
    }
    public function Table(){
        echo'<table>';
            echo'<tr>';
                echo'<th>Dzień</th>';
                echo'<th>Kurs kupna</th>';
                echo'<th>Kurs sprzedaży</th>';
                echo'<th>Różnica kupno</th>';
                echo'<th>Różnica sprzedaży</th>';
            echo'</tr>';
            echo'<tr>';
                echo'<td>'.$this->Request_Date.'</td>';
                $this->Currency_O->Show_Values();
            echo'</tr>';
        echo'</table';
    }
}