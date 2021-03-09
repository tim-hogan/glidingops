var devt = {
    version: 2.0,
    // **********************************************************************
    // JS FUNCTIONS
    // **********************************************************************
    def: function (x) {
        if (typeof x != 'undefined')
            return true;
        return false;
    },
    pad: function (v, l) {
        var s = v + "";
        while (s.length < l) s = "0" + s;
        return s;
    },
    nf: function (n, y) {
        var p = Math.pow(10, y);
        return Math.round(parseFloat(n) * p) / p;
    },
    isWholeNum: function (n) {
        return ((n - Math.floor(n)) === 0);
    },
    tedit: function (n, c, options) {
        var p = n.parentElement;
        var i = document.createElement('INPUT');
        i.value = n.innerHTML;
        if (devt.def(options.input.size))
           devt.sa(i, 'size', options.input.size);
        devt.sa(i, 'onkeydown', 'devt.tede(this)');
        devt.sa(i, 'original', n.innerHTML);
        devt.sa(i, 'prevtag', n.tagName);
        i.callback = c;
        p.appendChild(i);
        p.removeChild(n);
    },
    tede: function (n) {
        if (event.key === 'Enter' || event.keyCode == 27) {
            var p = n.parentElement;
            var i = document.createElement(n.getAttribute('prevtag'));
            p.appendChild(i);
            devt.sa(i, 'onclick', 'devt.tedit(this,' + n.callback.name + ')');
            if (event.keyCode == 27)
                i.innerHTML = n.getAttribute('original');
            else {
                i.innerHTML = n.value;
                n.callback(n.value, i);
            }
            p.removeChild(n);
        }
    },
    tselect: function (n, l, c, op) {
        var p = n.parentElement;
        var i = document.createElement('SELECT');
        var list = l;
        if (!list && n.selectlist)
            list = n.selectlist;
        if (devt.def(op)) {
            var o = devt.cea('OPTION', i);
            o.value = 0;
            o.innerHTML = op;
        }
        for (var k = 0; k < list.length; k++) {
            var o = devt.cea('OPTION', i);
            o.value = list[k]['id'];
            o.innerHTML = list[k]['name'];
        }
        i.value = n.innerHTML;
        devt.sa(i, 'onchange', 'devt.tselec(this)');
        devt.sa(i, 'original', n.innerHTML);
        devt.sa(i, 'prevtag', n.tagName);
        i.callback = c;
        i.selectlist = l;
        p.appendChild(i);
        p.removeChild(n);
    },
    tselec: function (n) {
        var p = n.parentElement;
        var i = document.createElement(n.getAttribute('prevtag'));
        devt.sa(i, 'onclick', 'devt.tselect(this,null,' + n.callback.name + ')');
        i.innerHTML = n.getAttribute('original');
        i.selectlist = n.selectlist;
        n.callback(n.value);
        p.appendChild(i);
        p.removeChild(n);
    },
    copyclip: function (data) {
        var b = document.getElementsByTagName('BODY')[0];
        var t = this.cea('TEXTAREA', b);
        t.value = data;
        t.select();
        document.execCommand('copy');
        b.removeChild(t);
    },
    // **********************************************************************
    // Dom Functions
    // **********************************************************************
    ge: function (t) {
        return document.getElementById(t);
    },
    ce: function (t) {
        return document.createElement(t);
    },
    cea: function (t, p) {
        var e = devt.ce(t);
        p.appendChild(e);
        return e;
    },
    ga: function (n, a) {
        return n.getAttribute(a);
    },
    sa: function (n, a, v) {
        n.setAttribute(a, v);
    },
    gebt: function (t) {
        return document.getElementsByTagName(t);
    },
    loadScript: function (src, callback) {
        var h = devt.gebt('head');
        for (var i = 0; i < h.length; i++) {
            var s = devt.gebt('script');
            for (var j = 0; j < s.length; j++) {
                if (s[j].src == src)
                    return;
            }
        }

        var s = devt.ce('script');
        s.type = 'text/javascript';
        s.src = src;
        if (devt.def(callback)) {
            s.onreadystatechange = callback;
            s.onload = callback;
        }
        devt.gebt('head')[0].appendChild(s);
    },
    removeAllChildren: function (n) {
        while (n.firstChild) {
            n.removeChild(n.firstChild);
        }
    },
    // **********************************************************************
    // API Functions
    // **********************************************************************
    apiJSON: function (host, base, key, useHTTPS) {

        var self = this;
        var server = new XMLHttpRequest();

        this.strHttp = 'http://';
        if (devt.def(useHTTPS) && useHTTPS)
            this.strHttp = 'https://';
        this.strHttp += host;
        if (devt.def(base) && base.length > 0) this.strHttp += '/' + base;
        if (devt.def(key) && key.length > 0) this.strHttp += '/' + key;
        this.http = server;
        this.reqQueue = [];

        this.queueReq = function (method, command, params) {
            var entry = {};
            entry['method'] = method;
            entry['command'] = command;
            entry['params'] = params;
            entry['timestamp'] = Date.now();
            this.reqQueue.push(entry);

            if (this.reqQueue.length == 1) {
                this.serverSend();
            }
            else
                this.onCheck();
        }

        this.replyRcvd = function () {
            this.reqQueue.shift();
            if (this.reqQueue.length > 0) {
                this.serverSend();
            }
        }

        this.serverSend = function () {
            if (this.reqQueue.length >= 1) {
                entry = this.reqQueue[0];
                if (entry['method'].toUpperCase() == 'GET') {
                    this.http.open("GET", this.strHttp + "/" + entry['command'], true);
                    this.http.send();
                }
                if (entry['method'].toUpperCase() == 'POST') {
                    this.http.open("POST", this.strHttp + "/" + entry['command'], true);
                    this.http.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
                    this.http.send(JSON.stringify(entry['params']));
                }
                if (entry['method'].toUpperCase() == 'PUT') {
                    this.http.open("PUT", this.strHttp + "/" + entry['command'], true);
                    this.http.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
                    this.http.send(JSON.stringify(entry['params']));
                }
            }
        }

        server.onreadystatechange = function () {
            if (server.readyState == 4 && server.status == 200) {
                var s = JSON.parse(server.responseText);
                strdebug = server.responseText;
                self.parseReply(s);
                //try { self.parseReply(s); } catch (e) { console.log('parseReply Failed'); };
                self.replyRcvd();
            }
        }

        this.onCheck = function () {
            //Checks the queue
            for (var z = 0; z < self.reqQueue.length; z++) {
                entry = self.reqQueue[z];
                var ts = new Date();
                if (entry['timestamp'] + 30000 < ts.getTime()) {
                    //this boy needs to be removed from the queue
                    console.log("remove from queue timeout " + entry['command']);
                    self.reqQueue.shift();
                    if (self.reqQueue.length > 0) {
                        self.serverSend();
                    }
                }
            }
        }
    }
};