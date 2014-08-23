Converts

```css
#my_element ul.test {
    list-style-type: none
}

#my_element ul.test {
    color: white;
    background: black;
}

#my_element ul.test .list-item a {
    text-decoration: none;
}

#my_element ul.test .list-item:last-child a {
    font-weight: bold;
}
```

into

```sass
#my_element ul.test {
    
    list-style-type: none;
    color: white;
    background: black;

    .list-item a {

        text-decoration: none;

        &:last-child {
            font-weight: bold;
        }
    }
}
```