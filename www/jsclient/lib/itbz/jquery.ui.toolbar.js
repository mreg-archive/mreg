/*
    depends:
        hotkeys, for keyboard shortcut support
*/

(function($){

    $.fn.toolbar = function(options) {

        options = $.extend({
            pos: 'top',
            animate: false,
            animateTime: 200,
            animatePos: '-45px'
        }, options);

        // Enable shortcuts from outset
        window.fShortcutsEnabled = true;

        return this.each(function() {
            var $this = $(this);

            $this.addClass('ui-widget-content itb-toolbar itb-toolbar-' + options.pos);
            
            // Animation support
            if ( options.animate ) {
                
                var inEffects = {
                    left: {left: 0},
                    right: {right: 0},
                    top: {top: 0},
                    bottom: {bottom: 0},
                };

                var outEffects = {
                    left: {left: options.animatePos},
                    right: {right: options.animatePos},
                    top: {top: options.animatePos},
                    bottom: {bottom: options.animatePos}
                };

                // Hide bar after 1 sec
                $this.delay(1000).animate(outEffects[options.pos], options.animateTime);

                // Standard mouse enter
                $this.mouseenter(function(){
                    var $el = $(this);
                    $.each(inEffects, function(pos, effect){
                        $el.parent().find('.itb-toolbar-' + pos)
                            .clearQueue()
                            .animate(effect, options.animateTime);
                    });
                });

                // Standard mouse leave
                $this.mouseleave(function(){
                    var $el = $(this);
                    $.each(outEffects, function(pos, effect){
                        $el.parent().find('.itb-toolbar-' + pos)
                            .clearQueue()
                            .animate(effect, options.animateTime);
                    });
                });
                
                // Custom bindings to show toolbar when mouse leaves window
                $(document).mouseleave(function(){
                    $this.mouseenter();
                });
                $('body').mouseleave(function(e){
                    if ( options.pos=='right' && e.pageX>=$('body').width() ) {
                        $this.mouseenter();
                    }
                });
            }

            // Regular buttons
            $this.find('button').each(function(){
                $(this).button();
            });

            // Non buttonset checkboxes
            $this.find('*').not('.itb-buttonset').find(':checkbox').each(function(){
                $(this).button();
            });

            // Buttonsets
            $this.find('.itb-buttonset').each(function(){
                $(this).buttonset();
            });

            // Bind hotkeys, set icon and ignore-label
            $this.find(':input').each(function(){
                var $input = $(this);

                var hotkey = $input.attr('data-hotkey');
                if ( hotkey ) {
                    $('*').live('keydown', hotkey, function(event){
                        if ( window.fShortcutsEnabled ) {
                            $input.click();
                        }
                        return false;
                    });
                }

                var icon = $input.attr('data-icon');
                if ( icon ) {
                    $input.button('option', 'icons', {primary: icon});
                }

                if ( $input.attr('data-ignore-label') ) {
                    $input.button('option', 'text', false);
                }
            });

        });
    };
})(jQuery);
