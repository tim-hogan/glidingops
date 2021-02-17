<div id="headcontainer">
    <div class="left">
        <p>Wellington Wairarapa Gliding Club</p>
        <p>operations</p>
    </div>
    <div class="right">
        <?php 
        $name='';
        if (isset($user['firstname']) )
            $name = $user['firstname'] . " ";
        if (isset($user['surname']) )
            $name .= $user['surname'];
        $name = htmlspecialchars(trim($name));
        echo "<p>{$name} Signed in as {$user['usercode']}</p>";
        ?>
    </div>
</div>