<?php

$testData = "

#my_element h1 {
    font-weight: bold;
}

#my_element ul.test {
    list-style-type: none
}

#my_element ul.test {
    color: white;
    background: black;
}

#my_element ul.test .list-item {
    text-decoration: none;
    color: #737373;
}

#my_element ul.test .list-item:last-child {
    font-weight: bold;
}
";

$expectedOutput = "

#my_element {


    h1 {
        font-weight: bold;
    }

    ul.test {
        list-style-type: none
        color: white;
        background: black;

        .list-item {
            text-decoration: none;
            color: #737373;
        }

        .list-item:last-child {
            font-weight: bold;
        }
    }
}";