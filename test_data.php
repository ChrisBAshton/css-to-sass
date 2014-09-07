<?php

$testData = "

#marina ul.test {
    list-style-type: none;
    color: white;
    background: black;
}

#marina ul.test .list-item a {
    text-decoration: none;
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