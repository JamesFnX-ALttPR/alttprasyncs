<?php
echo '        <script>' . PHP_EOL;
echo '            $( function() {' . PHP_EOL;
echo '                $.widget( "custom.iconselectmenu", $.ui.selectmenu, {' . PHP_EOL;
echo '                    _renderItem: function( ul, item ) {' . PHP_EOL;
echo '                        var li = $( "<li>" ),' . PHP_EOL;
echo '                        wrapper = $( "<div>", { text: item.label } );' . PHP_EOL . PHP_EOL;
echo '                        if ( item.disabled ) {' . PHP_EOL;
echo '                            li.addClass( "ui-state-disabled" );' . PHP_EOL;
echo '                        }' . PHP_EOL . PHP_EOL;
echo '                        $( "<span>", {' . PHP_EOL;
echo '                            style: item.element.attr( "data-style" ),' . PHP_EOL;
echo '                            "class": "ui-icon " + item.element.attr( "data-class" )' . PHP_EOL;
echo '                        })' . PHP_EOL;
echo '                        .appendTo( wrapper );' . PHP_EOL . PHP_EOL;
echo '                        return li.append( wrapper ).appendTo( ul );' . PHP_EOL;
echo '                    }' . PHP_EOL;
echo '                });' . PHP_EOL . PHP_EOL;
echo '                $( "#hash1" )' . PHP_EOL;
echo '                    .iconselectmenu({ width: 150 })' . PHP_EOL;
echo '                    .iconselectmenu( "menuWidget" )' . PHP_EOL;
echo '                        .addClass( "ui-menu-icons customicons overflow" );' . PHP_EOL . PHP_EOL;
echo '                $( "#hash2" )' . PHP_EOL;
echo '                    .iconselectmenu({ width: 150 })' . PHP_EOL;
echo '                    .iconselectmenu( "menuWidget" )' . PHP_EOL;
echo '                        .addClass( "ui-menu-icons customicons overflow" );' . PHP_EOL . PHP_EOL;
echo '                $( "#hash3" )' . PHP_EOL;
echo '                    .iconselectmenu({ width: 150 })' . PHP_EOL;
echo '                    .iconselectmenu( "menuWidget" )' . PHP_EOL;
echo '                        .addClass( "ui-menu-icons customicons overflow" );' . PHP_EOL . PHP_EOL;
echo '                $( "#hash4" )' . PHP_EOL;
echo '                    .iconselectmenu({ width: 150 })' . PHP_EOL;
echo '                    .iconselectmenu( "menuWidget" )' . PHP_EOL;
echo '                        .addClass( "ui-menu-icons customicons overflow" );' . PHP_EOL . PHP_EOL;
echo '                $( "#hash5" )' . PHP_EOL;
echo '                    .iconselectmenu({ width: 150 })' . PHP_EOL;
echo '                    .iconselectmenu( "menuWidget" )' . PHP_EOL;
echo '                        .addClass( "ui-menu-icons customicons overflow" );' . PHP_EOL . PHP_EOL;
echo '             } );' . PHP_EOL;
echo '        </script>' . PHP_EOL;