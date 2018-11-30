<?php
include("../../databaseClass.php");
$databaseClass = new databaseClass();
$conn = $databaseClass->connect();
//declare empty string
$emptyString = "";
//initilize some empty arrays
$emptyArr = array();
$emptyArr2 = array();
//get all the data from the database that holds the fdd id's that users download
$sql = "SELECT * FROM downloaded_fdds";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row and trim off all brackets and commas
    while($row = $result->fetch_assoc()) 
    {
        $trimed = trim($row['jsondata'],"[");
        $secondtrim = trim($trimed,"]");
        $thirdTrim = str_replace(",","", $secondtrim);
        
        //push all results into the first empty array
        array_push($emptyArr, $thirdTrim);      
    }
} 
foreach ($emptyArr as $value)
{
   //convert this array to a string
     $string .= $value;
}
//convert it back into a string array
$split = str_split($string, 1);


//this turns the string array into an int array which is needed for counting the most popular fdds
foreach($split as $value)
{  //convert to integer and push to second empty array
    $intValue = (int)$value;
    array_push($emptyArr2, $intValue);
}
//find the most popular fdd
$mp1 = array_count_values($emptyArr2); 
$mostPopular = array_search(max($mp1), $mp1);


//take away the most popular from the array so I can find second most popular
foreach (array_keys($emptyArr2, $mostPopular) as $key) {
    unset($emptyArr2[$key]);
}
//find second most popular and 
$mp2 = array_count_values($emptyArr2); 
$mostPopular2 = array_search(max($mp2), $mp2);

//unset the second most popular so the third can be found
foreach (array_keys($emptyArr2, $mostPopular1) as $key2) {
    unset($emptyArr2[$key2]);
}
//find third most popular
$mp2 = array_count_values($emptyArr2); 
$mostPopular3 = array_search(max($mp3), $mp3);
//unset third most popular so we can find the forth
foreach (array_keys($emptyArr2, $mostPopular3) as $key3) {
    unset($emptyArr2[$key3]);
}
//find fourth most popular,  no more need to unset
$mp4 = array_count_values($emptyArr2); 
$mostPopular4 = array_search(max($mp4), $mp4);


//sql statement to output four most popular FDDs
$sqlMostPopular = "SELECT * FROM `fdd` WHERE fdd_id = $mostPopular 
            union SELECT * from fdd WHERE fdd_id = $mostPopular2 
            union SELECT * from fdd WHERE fdd_id = $mostPopular3 
            union SELECT * from fdd WHERE fdd_id = $mostPopular4";
$resultMp = $conn->query($sqlMostPopular);

?>
<!--style the table-->
	<style type="text/css">
		
		table {
			/*margin: auto;*/
			font-family: "Lucida Sans Unicode", "Lucida Grande", "Segoe Ui";
			font-size: 12px;
		}

	

		table td {
			transition: all .5s;
		}
		
		/* Table */
		.data-table {
			border-collapse: collapse;
			font-size: 14px;
			min-width: 537px;
		}

		.data-table th, 
		.data-table td {
			border: 1px solid #e1edff;
			padding: 7px 17px;
		}
	

		/* Table Header */
		.data-table thead th {
			background-color: #508abb;
			color: #FFFFFF;
			border-color: #6ea1cc !important;
			text-transform: uppercase;
		}

		/* Table Body */
		.data-table tbody td {
			color: #353535;
		}
		

		.data-table tbody tr:nth-child(odd) td {
			background-color: #f4fbff;
		}
		.data-table tbody tr:hover td {
			background-color: #ffffa2;
			border-color: #ffff0f;
		}

		/* Table Footer */
		
		.data-table tfoot th:first-child {
			text-align: left;
		}
		.data-table tbody td:empty
		{
			background-color: #ffcccc;
		}
        #snackbar {
    
    
    
    color: #fff;
    text-align: center;
    border-radius: 2px;
    
    width: 50%;
    
    
   
    font-size: 17px;
    
}

	</style>
<table class="data-table">
		<thead>
			<tr>
            <th>Pick</th>
				<th>Franchise Name</th>
				<th>Price</th>
				<th>Franchise Category</th>
				<th>Type of Document</th>
				
			</tr>
		</thead>
		<tbody>
		<?php
		//output the data to the html table so the user sees, checkboxes the user can pick
		while ($row = $resultMp->fetch_assoc())
		{
			
            echo '<tr>
                    <td><input type ="checkbox" onclick="countResult('.$row['fdd_id'].');"></td>
					<td>'.$row['franchise_name'].'</td>
					<td>$'.$row['price'].'</td>
					<td>'.$row['franchise_category'].'</td>
					<td>'.$row['type_of_document'].'</td>
                   
                </tr>';
                
                
			
		}?>
		</tbody>
		
	</table>

<script type="text/javascript">
  
        
//declare counter and empty array
var clicks = 0.00;
var finalArr = [];


var wooCounter = 0;
//function has a counter for the checkboxes and an ajax call on submit
function countResult(id) 
{   //find index of id in the array
    arrIndex = finalArr.indexOf(id);
    

    //if element does not exist push it to array and increment counter
    if(arrIndex === -1)
    {   clicks = clicks + 19.95;
        wooCounter = wooCounter + 1;
        //grap element at top that holds counter and insert count
        document.getElementById("clicks").innerHTML = clicks.toFixed(2);
        document.getElementById("quantity_5b7db721bf3ae").value = wooCounter;
        
        //push id to the array
        finalArr.push(id);
        
        
    }

    if (arrIndex > -1) 
    {   clicks = clicks - 19.95;
        wooCounter = wooCounter - 1;
        //grab element at top that holds counter and decrese by one
        document.getElementById("clicks").innerHTML = clicks.toFixed(2);
        document.getElementById("quantity_5b7db721bf3ae").value = wooCounter;
      
        //remove element from array
        finalArr.splice(arrIndex, 1);
            
    }
    
    
  //ajax call that  inserts the users picks into a db which can be called later when the user downloads their FDD
  $("#wooform").submit(function() {
            
           var myJSON = finalArr.toString();
            

                $.ajax({
                    type: "POST",
                    url: "https://askmrfranchise.com/franchise_dashboard/model/insert.php",
                    data: "array=" + myJSON,
                    
                    
                });


            });
            //for testing when working in the webconsole
            console.log(finalArr);

        }
//if the array is empty output zero to the user
function setZero() {
    if (finalArr.length == 0){
    document.getElementById("clicks").innerHTML = "0.00";
   }
}
</script>

<div style="float: right;" id="product-2444" class="post-2444 product type-product status-publish product_cat-uncategorized post first instock virtual taxable purchasable product-type-simple">
<div class="woocommerce-product-gallery woocommerce-product-gallery--without-images woocommerce-product-gallery--columns-4 images" data-columns="4" style="opacity: 0; transition: opacity .25s ease-in-out;">
</div>
<div class="summary entry-summary">
	<!--form that submits to a woocommerce product upon checkout, shows the user a submit button allont with images of payments we take-->	
    <form class="cart" action="https://askmrfranchise.com/product/fdd" method="post" enctype='multipart/form-data'
    style="position: fixed; top: 120px;" id="wooform">
		
		<div class="quantity">
		<label class="screen-reader-text" for="quantity_5b7db721bf3ae">Quantity</label>
		<input
			type="hidden"
			id="quantity_5b7db721bf3ae"
			class="input-text qty text"
			step="1"
			min="1"
			max=""
			name="quantity"
			value="0"
			title="Qty"
			size="4"
			pattern="[0-9]*"
			inputmode="numeric"
			aria-labelledby="" /><center><p>Total: $<a id="clicks">0.00</a><span> Plus Tax</span><br>
            <img height='50' width='50' src='/wp-content/uploads/2018/08/paypalicon2.png'> 
             <img height='50' width='50' src='/wp-content/uploads/2018/08/amazonicon.png'>
             <img height='50' width='50' src='/wp-content/uploads/2018/08/applepayicon2.png'>
             <img style="visibility: hidden" height='50' width='50' src='/wp-content/uploads/2018/08/applepayicon2.png'>
             
            </p></center><center><button style="border-radius: 5px;
            background-color: orange;
            color: white;" type="submit" name="add-to-cart" value="2444" class="single_add_to_cart_button button alt">Checkout</button></center>
	    </div>
	</form>         
	</div>

