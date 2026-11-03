$(document).ready(function() {
    ///////////
    
    ///////////
    // Select the node that will be observed for mutations
    var targetNode = document.body;

    // Options for the observer (which mutations to observe)
    var config = { childList: true, subtree: true };

    // Callback function to execute when mutations are observed
    var callback = function(mutationsList, observer) {
        for(var mutation of mutationsList) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(function(node) {
                    // Ensure it's an element node
                    if(node.nodeType === 1) {
                        var $node = $(node); // Convert to jQuery object

                        if($node.prop('id') == 'newContentForm'){
                            customDate();
                        }
                    }
                });
            }
        }
    };

    // Create an observer instance linked to the callback function
    var observer = new MutationObserver(callback);

    // Start observing the target node for configured mutations
    observer.observe(targetNode, config);

    // Later, you can stop observing
    // observer.disconnect();
});
