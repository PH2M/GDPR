Validation.addAllThese([
    ['validate-password', 'Please enter valid password, without more than 3 options, numeric, lowercase, uppercase, special character', function(v) {
        return true;
        var pass        = v.strip(); /*strip leading and trailing spaces*/
        var errorCount  = 0;
        if(pass.length < 8 || pass.length > 30) {
            return false;
        }
        var numericRegex          = new RegExp(/[0-9]/);
        var lowercaseRegex        = new RegExp(/[a-z]/);
        var uppercaseRegex        = new RegExp(/[A-Z]/);
        var specialCharacterRegex = new RegExp(/[^a-zA-Z0-9$]/);
        if (v.match(numericRegex) === null) {
            errorCount++;
        }
        if (v.match(lowercaseRegex) === null) {
            errorCount++;
        }
        if (v.match(uppercaseRegex) === null) {
            errorCount++;
        }
        if (v.match(specialCharacterRegex) === null) {
            errorCount++;
        }
        return errorCount <= 1;
    }]
]);