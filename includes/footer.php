<?php

echo '        <hr />' . PHP_EOL;
if (isset($_SESSION['userid'])) {
    echo '        <div class="footer"><span class="footerleft">Logged in as ' . $_SESSION['displayName'] . '</span>';
} else {
    echo '        <div>';
}
echo '<span><script type="text/javascript" src="https://storage.ko-fi.com/cdn/widget/Widget_2.js"></script><script type="text/javascript">kofiwidget2.init("Tip JamesFnX", "#000000", "B0B6156Z29");kofiwidget2.draw();</script></span><span class="footerright">' . date("Y") . ' - Created by <a target="_blank" href="https://twitch.tv/jamesfnx">JamesFnX</a></span></div>';
echo '    </body>' . PHP_EOL;
echo '</html>' . PHP_EOL;
