var ChargesSelectTemplate = null;
var ChargesSelect = function(colname, collid, selvalue, options = {}) {
    var self = this;
    var sel = null;

    function buildSelect() {
        sel = document.createElement('select')
        sel.setAttribute('colname', colname)

        sel.onchange = function() {
            self.onValueSelected(sel.value)
            fieldchange(sel)
        }
        sel.id = collid;

        $(sel).addClass('combo-search')

        if (options.classes) {
            $(sel).addClass(options.classes)
        }

        buildOptions(selvalue)
    }

    function buildOptions(selvalue) {
        $(getTemplate()).clone().appendTo($(sel))
        $(sel).val(selvalue)
    }

    function getTemplate() {
        var frag = null;
        if(!ChargesSelectTemplate) {
            frag = document.createDocumentFragment()

            var parser = new DOMParser();
            chargeDoc = parser.parseFromString(chargeopts, "text/xml");
            if (null != chargeDoc) {
                var mems = chargeDoc.getElementsByTagName("ChargeOpts")[0].childNodes;
                for (i = 0; i < mems.length; i++) {
                    if (mems[i].nodeName == "opt") {
                        var id = mems[i].getElementsByTagName("id")[0].childNodes[0].nodeValue;
                        var desc = mems[i].getElementsByTagName("desc")[0].childNodes[0].nodeValue;
                        opt = document.createElement("option");

                        opt.value = "c" + id;
                        opt.innerHTML = desc;
                        frag.appendChild(opt);
                    }
                }
            }

            var parser2 = new DOMParser();
            membersDoc = parser2.parseFromString(allmembers, "text/xml");
            if (null != membersDoc) {
                var mems = membersDoc.getElementsByTagName("allmembers")[0].childNodes;
                for (i = 0; i < mems.length; i++) {

                    var id = mems[i].getElementsByTagName("id")[0].childNodes[0].nodeValue;
                    var name = mems[i].getElementsByTagName("name")[0].childNodes[0].nodeValue;
                    opt = document.createElement("option");

                    opt.value = "m" + id;
                    opt.innerHTML = name;
                    frag.appendChild(opt);
                }
            }

            ChargesSelectTemplate = frag;
        }

        return ChargesSelectTemplate
    }

    // ===========================================
    // Public interface
    // ===========================================
    function clear() {
        $(sel).html('')
    }

    self.onValueSelected = function(value) {
        // do nothing on select
    }

    self.value = function() {
        return sel.value
    }

    self.addTo = function(targetDomNode) {
        targetDomNode.appendChild(sel)
        $(sel).selectpicker({
            // header: 'Put a nice header heare'
            dropupAuto: false,
            dropdownAlignRight: false,
            size: 10,
            width: '100%',
            liveSearch: true,
        })
    }

    self.refresh = function() {
        var selectedValue = $(sel).val();
        clear()

        buildOptions(selectedValue);
        $(sel).selectpicker('refresh');
    }

    buildSelect()
}
