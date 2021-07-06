<?php
for ($i=1;$i<=100;$i++){
    $num = $i;
    if ($i%5 == 0 && $i%3 ==0){
        $num = "Foobar";
    }else if ($i%3 == 0){
        $num = "Foo";
    }else if ($i%5 == 0){
        $num = "Bar";
    }
    echo $num. " \t";

  //another solution using contatenation
    // $output = "";
    // if ($i%3 === 0){ $output .= "Foo"; }
    // if ($i%5 === 0){ $output .= "Bar"; }
    // if ($output === ""){ $output .= $i; }
    //     echo $output." \t";
}
  echo " \n";

?>