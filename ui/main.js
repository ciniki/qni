//
// New App
//
function ciniki_qni_main() {
    //
    // qni panel
    //
    // mc = container within the UI kinda like the finder in mac
    // medium is a medium sized panel
    // every panel must say sectioned, these could bceome other types later but all are sectioned for now
    // ciniki.qni.main.menu is the help reference for this panel
    this.menu = new M.panel('Module', 'ciniki_qni_main', 'menu', 'mc', 'medium', 'sectioned', 'ciniki.qni.main.menu');
    this.menu.sections = {
        'nets':{'label':'Nets', 'type':'simplegrid', 'num_cols':1, 
            // You can leave out the headervalue if no headers are desired
            'headerValues':['Name'],
            // Nothing is retured from the API
            'noData':'No Nets',
            'addTxt':'Add Net',
            // This is the function to be called when clicking "Add" and the 0 ID means we are adding a new one
            'addFn':'M.ciniki_qni_main.net.edit(\'M.ciniki_qni_main.menu.open();\',0);',
            },
    };
    this.menu.noData = function(s) { return this.sections[s].noData; }
    // This is what shows values in the cells of the grid
    // s=section, i=row, j=column, d=data
    this.menu.cellValue = function(s, i, j, d) {
        if( s == 'nets' ) {
            switch (j) {
                case 0: return d.name;
            }
        }
    };
    // If they click on a row in the UI, then what should it do?
    this.menu.rowFn = function(s, i, d) {
        if( s == 'nets' ) {
            return 'M.ciniki_qni_main.net.edit(\'M.ciniki_qni_main.menu.open();\',\'' + d.id + '\');';
        }
    };
    this.menu.open = function(cb) {
        this.data = {};
        // This callback gets run when it finishes the call to the API
        M.api.getJSONCb('ciniki.qni.netList', {'business_id':M.curBusinessID}, function(rsp) {
            if( rsp.stat != 'ok' ) {
                M.api.err(rsp);
                return false;
            }
            var p = M.ciniki_qni_main.menu;
            // rsp.nets is an array and each evelment of the nets array has data for each net
            p.data = rsp;
            p.refresh();
            p.show(cb);
        });
    };
    this.menu.addClose('Back');

    //
    // The panel for editing a net
    //
    this.net = new M.panel('Net', 'ciniki_qni_main', 'net', 'mc', 'medium', 'sectioned', 'ciniki.qni.main.net');
    this.net.data = {};
    this.net.net_id = 0;
    this.net.sections = { 
        // fields are DB fields and we are telling the interface to show these as editable
        'general':{'label':'Net', 'aside':'yes', 'fields':{
            'name':{'label':'Name', 'type':'text'},
            'net_type':{'label':'Type', 'type':'select', 'options':{'20':'Emergency','40':'Priority','60':'Health and Welfare','80':'Practice Weekly','100':'Practice Monthly','200':'Ad Hoc'}},
            // I can define any flags I need based on use cases
            'flags':{'label':'Options', 'type':'flags', 'flags':{'1':{'name':'Visible'}}},
            'status':{'label':'Status', 'type':'toggle', 'toggles':{'10':'Active','50':'Archive','60':'Deleted'}},
            }}, 
        '_buttons':{'label':'', 'buttons':{
            'save':{'label':'Save', 'fn':'M.ciniki_qni_main.net.save();'},
            'delete':{'label':'Delete', 'visible':function() {return M.ciniki_qni_main.net.net_id>0?'yes':'no';}, 'fn':'M.ciniki_qni_main.net.remove();'},
            }},
        };  
    this.net.fieldValue = function(s, i, d) { return this.data[i]; }
    // How you get a history of the field if diffrent values existed before
    this.net.fieldHistoryArgs = function(s, i) {
        return {'method':'ciniki.qni.netHistory', 'args':{'business_id':M.curBusinessID, 'net_id':this.net_id, 'field':i}};
    }
    this.net.edit = function(cb, id) {
        this.reset();
        if( id != null ) { this.net_id = id; }
        M.api.getJSONCb('ciniki.qni.netGet', {'business_id':M.curBusinessID, 'net_id':this.net_id}, function(rsp) {
            if( rsp.stat != 'ok' ) {
                M.api.err(rsp);
                return false;
            }
            var p = M.ciniki_qni_main.net;
            p.data = rsp.net;
            p.refresh();
            p.show(cb);
        });
    }
    this.net.save = function(cb) {
        if( cb == null ) { cb = 'M.ciniki_qni_main.net.close();'; }
        if( this.net_id > 0 ) {
            // if 0 then add
            var c = this.serializeForm('no');
            // If c != '' then changes have been made in this panel
            if( c != '' ) {
                M.api.postJSONCb('ciniki.qni.netUpdate', {'business_id':M.curBusinessID, 'net_id':this.net_id}, c,
                    function(rsp) {
                        if( rsp.stat != 'ok' ) {
                            M.api.err(rsp);
                            return false;
                        } 
                        eval(cb);
                    });
            } else {
                eval(cb);
            }
        } else {
            // if not 0 then update
            var c = this.serializeForm('yes');
            M.api.postJSONCb('ciniki.qni.netAdd', {'business_id':M.curBusinessID, 'net_id':this.net_id}, c,
                function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    } 
                    M.ciniki_qni_main.net.net_id = rsp.id;
                    eval(cb);
                });
        }
    };
    this.net.remove = function() {
        if( confirm('Are you sure you want to remove this net?') ) {
            M.api.getJSONCb('ciniki.qni.netDelete', {'business_id':M.curBusinessID, 'net_id':this.net_id}, function(rsp) {
                if( rsp.stat != 'ok' ) {
                    M.api.err(rsp);
                    return false;
                } 
                M.ciniki_qni_main.net.close();
            });
        }
    };
    this.net.addButton('save', 'Save', 'M.ciniki_qni_main.net.save();');
    this.net.addClose('Cancel');

    // This is a special function that is always called when the app is loaded
    this.start = function(cb, appPrefix, aG) {
        args = {};
        if( aG != null ) { args = eval(aG); }

        //
        // Create the app container if it doesn't exist, and clear it out
        // if it does exist.
        //
        var appContainer = M.createContainer(appPrefix, 'ciniki_qni_main', 'yes');
        if( appContainer == null ) {
            alert('App Error');
            return false;
        } 

        this.menu.open(cb);
    }
};
