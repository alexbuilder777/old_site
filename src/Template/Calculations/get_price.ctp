<table class="table-base">
<tr>
<th>Название</th>
<th>Количество (м)</th>
<th>Стоимость</th>
</tr>



<?php
    $price = $this->request->data['width'] * $data['price'];
    $priceTotal = $price;
    $goalMessage = '';
    
    echo '<tr>';
    echo '<td>'.$data['name'].'</td>';
    echo '<td  style="text-align: center;">'.$this->request->data['width'].'</td>';
    echo '<td  style="text-align: center;">'.number_format($price, 0, '.', ' ').'</td>';
    echo '</tr>';




if(!empty($this->request->data['goals'])) {

    if($this->request->data['width'] >= 100) {
        $present = 1;
        $goals = $this->request->data['goals'] - 1;
        $goalMessage = 'Ворота в подарок (1 шт)';
    } else {
	    $goals = $this->request->data['goals'];
    }


    $priceGoals = $goals * $this->request->data['goalsPrice'];
    $priceTotal += $priceGoals;

    echo '<tr>';
    echo '<td>Ворота</td>';
    echo '<td  style="text-align: center;">'.$this->request->data['goals'].' шт</td>';
    echo '<td style="text-align: center;">'.$goalMessage.'</td>';
    echo '</tr>';
    	
}


if(!empty($this->request->data['count_wickets']) & !isset($present)) {


    if($this->request->data['width'] >= 50) {
        $Wickets = $this->request->data['count_wickets'] - 1;
        $WicketsMessage = 'Калитка в подарок (1 шт)';
    } else {
	    $Wickets = $this->request->data['count_wickets'];
    }


    $priceWickets = $Wickets * $this->request->data['countPrice'];
    $priceTotal += $priceWickets;

    echo '<tr>';
    echo '<td>Калитки</td>';
    echo '<td  style="text-align: center;">'.$this->request->data['count_wickets'].' шт</td>';
    echo '<td  style="text-align: center;">'.$WicketsMessage.'</td>';
    echo '</tr>';	
}


?>
<tr>
<td></td>
<td  style="text-align: center;">Итого &mdash;</td>
<td  style="text-align: center;"><?=number_format($priceTotal, 0, '.', ' ')?></td>
</tr>
</table>
<div style="color: green;">В стоимость включена: доставка, установка и материал.</div>