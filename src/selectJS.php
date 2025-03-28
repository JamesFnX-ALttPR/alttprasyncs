        <script>
            $( function() {
                $.widget( "custom.iconselectmenu", $.ui.selectmenu, {
                    _renderItem: function( ul, item ) {
                        var li = $( "<li>" ),
                        wrapper = $( "<div>", { text: item.label } );

                        if ( item.disabled ) {
                            li.addClass( "ui-state-disabled" )
                        }
                        
                        $( "<span>", {
                            style: item.element.attr( "data-style" ),
                            "class": "ui-icon " + item.element.attr( "data-class" )
                        })
                        .appendTo( wrapper );
                        
                        return li.append( wrapper ).appendTo( ul );
                    }
                })
                
                $( "#hash1" )
                    .iconselectmenu({ width: 150 })
                    .iconselectmenu( "menuWidget" )
                        .addClass( "ui-menu-icons customicons overflow" );
                        
                $( "#hash2" )
                    .iconselectmenu({ width: 150 })
                    .iconselectmenu( "menuWidget" )
                        .addClass( "ui-menu-icons customicons overflow" );

                $( "#hash3" )
                    .iconselectmenu({ width: 150 })
                    .iconselectmenu( "menuWidget" )
                        .addClass( "ui-menu-icons customicons overflow" );

                $( "#hash4" )
                    .iconselectmenu({ width: 150 })
                    .iconselectmenu( "menuWidget" )
                        .addClass( "ui-menu-icons customicons overflow" );

                $( "#hash5" )
                    .iconselectmenu({ width: 150 })
                    .iconselectmenu( "menuWidget" )
                        .addClass( "ui-menu-icons customicons overflow" );

             } );
        </script>
