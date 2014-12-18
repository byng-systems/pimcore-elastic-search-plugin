pimcore.registerNS("pimcore.plugin.elasticsearch");

pimcore.plugin.elasticsearch = Class.create(pimcore.plugin.admin, {
    getClassName: function() {
        return "pimcore.plugin.elasticsearch";
    },

    initialize: function() {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params,broker){
        //alert("Ready!");
    }
});

var elasticsearchPlugin = new pimcore.plugin.elasticsearch();

