<div id="headcontainer">
    <div class="left">
        <p class="phd1">Wellington Wairarapa Gliding Club</p>
        <p class="phd2">operations</p>
    </div>
    <div class="right">
        <?php 
        $name='';
        if (isset($user['firstname']) )
            $name = $user['firstname'] . " ";
        if (isset($user['surname']) )
            $name .= $user['surname'];
        $name = htmlspecialchars(trim($name));
        echo "<p class='phd3'>{$name} Signed in as {$user['usercode']}</p>";
        ?>
    </div>
</div>