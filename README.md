### Improvements

Location parsing should be more rigorous. Firstly by using other types of typed tokens to parse location such as T_IDENTIFIER_SPECIFIER:

    - check if T_LOGIC token is present ('in')
    - check the T_IDENTIFIER token that follows it
    
This is likely to be a location specifier. If neither a T_LOCATION (capitalized first letter) or a specifier token ('in') is found, then the 
app should loop over any T_IDENTIFIER tokens greater than a specific string length, and attempt to get an API location ID from there. 

The parsing could be more strict in all senses by taking in to account other typed tokens like in the above example which increases the 
semantic meaning behind a possible parsed parameter.

Caching location ID's in a DB or flat file instead of making a network request for every incoming search request.

It should have tests ;) 
