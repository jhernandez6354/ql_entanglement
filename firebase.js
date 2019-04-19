token='fbaY9wMY-w8%3aAPA91bElZTsoYyP-HuixdtSAWd65vfx7WqNfk4ycCaAwz2_VFlot-Bh68B8r80FoxLGZyvCT1qXaQBKIiXP9HJWmSbzEz-HmT8QmDSQeXfBblufUIQr3dukpCEbSjyPeHo1jyEFlhaB6%26'
firebase.auth().signInWithCustomToken(token).catch(function(error) {
    // Handle Errors here.
    var errorCode = error.code;
    var errorMessage = error.message;
    // ...
  });
