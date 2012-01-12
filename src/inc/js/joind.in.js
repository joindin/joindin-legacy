var joindIn = (function ($, window, document, undefined) {
    return {
        'apiRequest': function (rtype, raction, data, callback) {
            var actionNode = {
                'name': 'action',
                'attribs': {
                    'type': raction,
                    'output': 'json'
                }
            },
            xml_str = '<request>' + joindIn.toXML(actionNode, data) + '</request>',
            gt_url = "/api/" + rtype + '?reqk=' + reqk + '&seck=' + seck;
            
            $.ajax({
                type: "POST",
                url	: gt_url,
                data: xml_str,
                contentType: "text/xml",
                processData: false,
                dataType: 'json',
                success: function (rdata) {
                    var obj = rdata, targetLocation = '';
                    
                    //check for the redirect
                    if (obj.msg && obj.msg.match('redirect:')) {
                        targetLocation = obj.msg.replace(/redirect:/, '');
                        document.location.href = targetLocation;
                    } else {
                        //maybe add some callback method here 
                        //notifications.alert('normal'); 
                        if ($.isFunction(callback)) {
                            callback(obj);
                        }
                    }
                }
            });
        },
        'toXML': function (parentNode, data) {
            var xmlString = '', strNodeName = '', parentXML = '', key = '';
            $.each(data, function (k, v) {
                xmlString += '<' + k + '>' + v + '</' + k + '>';
            });
            if (typeof parentNode === 'string') {
                xmlString = parentNode.replace('%xml%', xmlString);
            } else if (typeof parentNode === 'object') {
                if (undefined !== parentNode.name) {
                    strNodeName = parentNode.name;
                    delete parentNode.name;
                }
                if (undefined !== parentNode.attribs) {
                    parentNode = parentNode.attribs;
                }
                for (key in parentNode) {
                    if (parentNode.hasOwnProperty(key)) {
                        if ('' === strNodeName) {
                            strNodeName = parentNode[key];
                            continue;
                        }
                        parentXML += ' ' + key + '="' + parentNode[key] + '"';
                    }
                }
                parentXML = '<' + strNodeName + '' + parentXML + '>%xml%</' + strNodeName + '>';
                xmlString = parentXML.replace('%xml%', xmlString);
            }
            return xmlString;
        }
    };
}(jQuery, window, document));