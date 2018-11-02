Validation.addAllThese([
    ['validate-gdpr-password', 'Your password must respect at least 3 of the following conditions: one capital letter, one lowercase letter, one number, one special character and contains at least 8 characters.', function(v) {
        var pass        = v.strip();
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
