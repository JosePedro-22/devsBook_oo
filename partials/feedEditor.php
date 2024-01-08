<div class="box feed-new">
    <div class="box-body">
        <div class="feed-new-editor m-10 row">
            <div class="feed-new-avatar">
                <img src="<?=$base?>/media/avatars/<?=$userInfo->avatar?>" />
            </div>
            <div class="feed-new-input-placeholder">O que você está pensando, <?=$userInfo->name?>?</div>
            <div class="feed-new-input" contenteditable="true"></div>
            <div class="feed-new-photo">
                <img src="<?=$base?>/assets/images/photo.png" />
                <input type="file" class="feed-input-photo" name="photo" accept="image/png, image/jpg, image/jpeg" />
            </div>

            <div class="feed-new-send">
                <img src="<?=$base?>/assets/images/send.png" />
            </div>

            <form class="feed-new-form" method="POST" action="<?=$base;?>/FeedEditorAction.php">
                <input type="hidden" name="body">
            </form>
        </div>
    </div>
</div>
<script>
let feedInput = document.querySelector('.feed-new-input');
let feedSubmit = document.querySelector('.feed-new-send');
let photoSubmit = document.querySelector('.feed-new-photo');
let feedForm = document.querySelector('.feed-new-form');
let inputPhoto = document.querySelector('.feed-input-photo');

photoSubmit.addEventListener('click', function(){
    inputPhoto.click();
});
inputPhoto.addEventListener('change', async function(){
    
    let photo = inputPhoto.files[0];
    let formData = new FormData();

    formData.append('photo', photo);
    let req = await fetch('<?=$base;?>/ajax_upload.php', {
        method: 'POST',
        body: formData
    });

    let json = await req.json();

    if(json.error != ''){
        alert(json.error);
    }

    window.location.href = window.location.href;
});

feedSubmit.addEventListener('click', function(){
    let value = feedInput.innerHTML.trim();

    feedForm.querySelector('input[name=body]').value = value;
    feedForm.submit();
});

</script>