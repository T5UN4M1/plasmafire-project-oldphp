<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */ 



?> 
<?php $loc = $planet->getPlanetLocation();?>
<table class="content2" id="galaxySwitcher">
    <tr><th>Galaxie</th><th>Systeme</th></tr>
    <tr><td>
    
    <input type="button" onclick="change('galaxy',-1)" value="<<" />
    <input type="text" id="galaxy" onkeyup="galaxyCheckValues()" value="<?php echo (!empty($_GET['g'])) ? $_GET['g'] : $loc->getG()?>" />
    <input type="button" onclick="change('galaxy',1)" value=">>" />
    
    </td><td>
    
    <input type="button" onclick="change('system',-1)" value="<<" />
    <input type="text" id="system" onkeyup="galaxyCheckValues()" value="<?php echo (!empty($_GET['s'])) ? $_GET['s'] : $loc->getS()?>" />
    <input type="button" onclick="change('system',1)" value=">>" />
    
    </td></tr>
    <tr><td colspan="2"><input type="button" onclick="getGalaxyData()" value="Go" /></td></tr>
</table>

<table class="content2" id="galaxyContent">
</table>
<table class="content"><tr><td id="galaxyMsg"></td></tr>
</table>


<script>

var revData = {
    "maxg" : <?php echo $config['maxG']?>,
    "maxs" : <?php echo $config['maxS']?>,
    "maxp" : <?php echo $config['maxP']?>,
    "skin" : "<?php echo $_SESSION['skin']?>"
}
</script>
<script src="./script/galaxy.js"></script>