<?php
/*put this at the bottom of the page so any templates
 populate the flash variable and then display at the proper timing*/
?>
<div class="container" id="flash" style="margin-top: 3em">
    <?php $messages = getMessages();?>
    <?php if ($messages): ?>
        <?php foreach ($messages as $msg): ?>
            <div class="alert alert-dark" role="alert">
                <p><?php echo $msg; ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<script>
    //used to pretend the flash messages are below the first nav element
    function moveMeUp(ele) {
        let target = document.getElementsByTagName("nav")[0];
        if (target) {
            target.after(ele);
        }
    }

    moveMeUp(document.getElementById("flash"));
</script>
