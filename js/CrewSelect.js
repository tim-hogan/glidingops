var MemberSelectTemplate = null;
var MemberSelect = function(colname, collid, selvalue, newvalLabel, options = {}) {
    var self = this;
    var listtag = "allmembers"
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
        var opt = document.createElement("option");
        opt.value = "0";
        opt.text = "--";
        sel.appendChild(opt);

        opt = document.createElement("option");
        opt.value = "new";
        opt.text = newvalLabel;
        sel.appendChild(opt);

        // sel.appendChild($(getTemplate()).clone());
        $(getTemplate()).clone().appendTo($(sel))

        $(sel).val(selvalue)
    }

    function getTemplate() {
        var frag = null;
        if(!MemberSelectTemplate) {
            frag = document.createDocumentFragment()
            parser = new DOMParser();
            dropDoc = parser.parseFromString(allmembers, "text/xml");
            if (null != dropDoc) {
                var mems = dropDoc.getElementsByTagName(listtag)[0].childNodes;
                for (i = 0; i < mems.length; i++) {
                    var id = mems[i].getElementsByTagName("id")[0].childNodes[0].nodeValue;
                    var name = mems[i].getElementsByTagName("name")[0].childNodes[0].nodeValue;

                    opt = document.createElement("option");
                    opt.value = id;
                    opt.innerHTML = name;
                    frag.appendChild(opt);
                }
            }
            MemberSelectTemplate = frag;
        }

        return MemberSelectTemplate;
    }

    self.onValueSelected = function(value) {
        // do nothing on select
    }

    function clear() {
        $(sel).html('')
    }

    self.refresh = function() {
        var selectedValue = $(sel).val();
        clear()

        buildOptions(selectedValue);
        $(sel).selectpicker('refresh');
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

    buildSelect()
}
