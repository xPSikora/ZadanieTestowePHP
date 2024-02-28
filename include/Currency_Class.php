<?php
Class Currency{
    public $NBP_Data;       //Bazowy obiekt
    public $Rate_Dates;     //Tablica obiektów kursów walut bazująca na datach
    public function __construct($NBP_Data){        
        $this->NBP_Data = $NBP_Data;
    }
    public function Set_Data($XML_Data){
        foreach($XML_Data->Rates->Rate as $Rate_O){
            if(!isset($this->Rate_Dates[str_replace('-','',$Rate_O->EffectiveDate)])){
                $this->Rate_Dates[str_replace('-','',$Rate_O->EffectiveDate)] = new Rate_Date($this);
                $this->Rate_Dates[str_replace('-','',$Rate_O->EffectiveDate)]->Set_Data($Rate_O);
            }
        }
    }
    public function Show_Values(){
        $this->Rate_Dates[str_replace('-','',$this->NBP_Data->Request_Date)]->Show_Values();
    }
    public function Check_Difference(){
        $this->Rate_Dates[str_replace('-','',$this->NBP_Data->Request_Date)]->Check_Difference($this->NBP_Data->Previous_Date);    
    }
}
Class Rate_Date{
    public $Currency_O;     //Obiekt waluty
    public $Date;           //Data kursu
    public $Buy_Value;      //Cena zakupu
    public $Buy_Diff;       //Róznica ceny zakupu względem poprzedniej daty
    public $Sell_Value;     //Cena sprzedaży
    public $Sell_Diff;      //Róznica ceny sprzedaży względem poprzedniej daty
    public function __construct($Currency_O){
        $this->Currency_O = $Currency_O;
    }
    public function Set_Data($Rate_O){
        $this->Date         = $Rate_O->EffectiveDate;
        $this->Buy_Value    = $Rate_O->Ask;
        $this->Sell_Value   = $Rate_O->Bid;
    }
    public function Check_Difference($Previous_Rate){
        $Prev_Rate_O = $this->Currency_O->Rate_Dates[str_replace('-','',$Previous_Rate)];
        $this->Buy_Diff     = round($this->Buy_Value - $Prev_Rate_O->Buy_Value,4); 
        $this->Sell_Diff    = round($this->Sell_Value - $Prev_Rate_O->Sell_Value,4);
    }
    public function Show_Values(){
        echo'<td>'.$this->Buy_Value.'</td>';
        echo'<td>'.$this->Sell_Value.'</td>';
        echo'<td>'.$this->Buy_Diff.'</td>';
        echo'<td>'.$this->Sell_Diff.'</td>';
    }
}