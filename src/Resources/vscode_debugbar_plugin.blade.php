<style>
    .phpdebugbar-widgets-list-item {align-items: baseline !important;}
    .phpdebugbar-plugin-vscodebutton {font-size: 12px !important;display: inline-block !important;color: #3f6ad8 !important;padding-top: 4px !important;margin-left: 8px !important;margin-right: 4px !important;cursor: pointer !important;}
    .phpdebugbar-plugin-vscodebutton:hover { color: #3c64cb !important; font-size: 1.0111em !important;}
</style>
<?php $isLinux = DIRECTORY_SEPARATOR === '/'; ?>
<script>
    var isLinux = {{ $isLinux ? 'true' : 'false' }};
    var extraSlash = (isLinux) ? '/' : '';

    // var phpdebugbar_plugin_vscode_mIsLoaded = false;

    function phpdebugbar_plugin_onBtnVscodeClicked(ev, el) {
        window.location.href = $(el).data('link');
        event.stopPropagation();
    }

    var phpdebugbar_plugin_vscode_onInit = function () {
        if ($) {
            // OK
        } else {
            // jQuery not yet available
            return;
        }

        if ($('.phpdebugbar-openhandler-overlay').is(":visible")) {
            return;
        }

        if ($('.phpdebugbar').length) {
            // OK
        } else {
            // laravel-debugbar not yet available
            return;
        }

        if ($('.phpdebugbar').hasClass('already-binded')) {
            return;
        }

        $('.phpdebugbar').addClass('already-binded');

        if ($('.phpdebugbar-open-btn').length) {
            if (!$('.phpdebugbar-open-btn').hasClass('click-listened')) {
                $('.phpdebugbar-open-btn').addClass('click-listened');
                $('.phpdebugbar-open-btn').click(function () {
                    $('.phpdebugbar').removeClass('already-binded');
                });
            }
        }

        if ($('.phpdebugbar-datasets-switcher').length) {
            if (!$('.phpdebugbar-datasets-switcher').hasClass('click-listened')) {
                $('.phpdebugbar-datasets-switcher').addClass('click-listened');
                $('.phpdebugbar-datasets-switcher').change(function () {
                    $('.phpdebugbar').removeClass('already-binded');
                });
            }
        }

        $(function onDocumentReady() {
            function getSchemeName() {
                return "{{ $editorName }}";
            }

            function getBasePath() {
                return "{{ str_replace('\\', '/', base_path()) }}" + extraSlash;
            }

            function isPhp(str) {
                return str.indexOf('.php') != -1;
            }

            function isController(str) {
                return str.indexOf('.php:') != -1;
            }

            function isBlade(str) {
                return str.indexOf('.blade.php') != -1 && str.indexOf('vscode_debugbar_plugin.blade.php') == -1 
            }

            function getLink(str) {
                var result = '';

                result += getSchemeName();
                result += '://file/';
                
                result += getBasePath();

                if (isBlade(str)) {
                    var iRes = str.indexOf('resources');
                    if (iRes != -1) {
                        if (!isLinux) {
                            // (\resources...)
                            iRes--; // to remove '\'
                        }
                        str = str.substring(iRes);
                        var iViews = str.indexOf('views');
                        if (iViews != -1) {
                            var iEnd = str.indexOf(')', iViews);
                            if (iEnd != -1) {
                                str = str.substring(0, iEnd);
                                result += str;
                            }
                        }
                    }
                } else if (isController(str)) {
                    var iRes = str.indexOf('.php:');
                    if (iRes != -1) {
                        var iLastDash = str.lastIndexOf('-');
                        var iFirstSignal = str.indexOf('>') + 1;
                        result += str.substring(iFirstSignal, iLastDash);
                    }
                }

                return result;
            }

            var funOnHoverIn = function (e) {
                e.stopPropagation();
                
                var str = $(this).html();
                if (isPhp(str) || isBlade(str) || isController(str)) {
                    // OK
                } else {
                    // Unknown format
                    return;
                }

                if (str.indexOf('vscode_debugbar_plugin') == -1) {
                    // OK
                } else {
                    // Don't add button to this plugin view path
                    return;
                }

                var strFullPath = getLink(str);
                var prefEditor = getSchemeName();

                if (isBlade(str)) {
                    var oldHtml = $(this).parent().html();
                    var strNewLink = '';
                    
                    if (oldHtml.indexOf('phpdebugbar-plugin-vscodebutton') == -1) {
                        strNewLink = '<a class="phpdebugbar-plugin-vscodebutton" onclick="phpdebugbar_plugin_onBtnVscodeClicked(event, this);" data-link="' + strFullPath + '" title="' + strFullPath + '">View in ' + prefEditor + ' </a>';
                    }
                    
                    $(strNewLink).insertAfter($(this));
                    
                } else if (isController(str)) {
                    var oldHtml = $(this).html();                    
                    var strNewLink = '';
                    
                    if (oldHtml.indexOf('phpdebugbar-plugin-vscodebutton') == -1) {
                        strNewLink = '<a class="phpdebugbar-plugin-vscodebutton" onclick="phpdebugbar_plugin_onBtnVscodeClicked(event, this);" data-link="' + strFullPath + '"  title="' + strFullPath + '">' +  '&#9998;' +   '</a>';
                        $(this).find('a').attr('href', strFullPath)
                    }
                }
            };

            var funOnHoverOut = function (e) {
                e.stopPropagation();
            };

            $('.phpdebugbar span.phpdebugbar-widgets-name').hover(funOnHoverIn, funOnHoverOut);
            $('.phpdebugbar dd.phpdebugbar-widgets-value').hover(funOnHoverIn, funOnHoverOut);
            
            $('.phpdebugbar span.phpdebugbar-widgets-name').each(function() {
                $(this).mouseover()
            });

            $('.phpdebugbar dd.phpdebugbar-widgets-value').each(function() {
                $(this).mouseover()
            });
                
        });

    }

    var phpdebugbar_plugin_vscode_mInterval = setInterval(phpdebugbar_plugin_vscode_onInit, 2000);
</script>
