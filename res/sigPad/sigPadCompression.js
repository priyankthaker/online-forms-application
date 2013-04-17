    // Signature inflation/deflation
    var inflateToJsonSignature = function (deflatedSig) {
        var intArray = [];
        for (var i = 0; i < deflatedSig.length; i++) {
            intArray.push((deflatedSig[i].charCodeAt()).toString(16));
        }

        var sigString = "[";
        for (var j = 0; j < intArray.length; j = j + 4) {
            sigString += (
                '{"lx":' + intArray[j] +
                ',"ly":' + intArray[j + 1] +
                ',"mx":' + intArray[j + 2] +
                ',"my":' + intArray[j + 3] + '},');
        }
        return sigString.substring(0, (sigString.length - 1)) + "]";
    };

    var deflateFromJsonSignature = function (jsonSig) {
        var replacedSig = jsonSig.substring(2, jsonSig.length - 2).replace(/"lx":/g, "")
            .replace(/"ly":/g, "").replace(/"mx":/g, "").replace(/"my":/g, "")
            .replace(/},{/g, ",");
        var compressString = "";
        var components = replacedSig.split(',');
        for (var i = 0; i < components.length; i++) {
            compressString += String.fromCharCode(parseInt(components[i], 16));
        }
        return compressString;
    };