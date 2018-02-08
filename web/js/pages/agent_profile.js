(function ($) {

    var image = $('#savingImage');
    var dialog = $('#imageDialogForm');
    var dialogImage = $('#imageDialogImage');
    var dialogCropButton = dialog.find('button.btn-primary');
    var cropImageButton = $('#savingImageCrop');
    var cropImageDataInput = $('#cropImageData');

    var openCropDialog = function () {
        dialogImage.cropper('destroy');

        dialog.modal();
        dialog.on('shown.bs.modal', function() {
            dialog.css('top','0');
            dialog.modal('handleUpdate');

            var initialCropperData = null;
            if (cropImageDataInput.val().length > 0) {
                // Cropper is already initialized
                initialCropperData = image.cropper('getData', true);
            }

            dialogImage.cropper({
                viewMode: 2,
                data: initialCropperData,
                aspectRatio: 4 / 3,
                dragMode: 'move',
                autoCropArea: 1,
                rotatable: false
            });
        });
    };

    dialogCropButton.on('click', function () {
        var cropData = dialogImage.cropper('getData', true);
        cropImageDataInput.val(JSON.stringify(cropData));

        image.attr('src', dialogImage.attr('src'));
        image.cropper('destroy');
        image.removeClass('hidden');
        image.cropper({
            viewMode: 2,
            data: cropData,
            rotatable: false,
            built: function () {
                image.cropper('disable');
            }
        });
        cropImageButton.parent('div').removeClass('hidden');

        dialog.modal('hide');
    });

    cropImageButton.on('click', function () {
        dialogImage.attr('src', image.attr('src'));
        openCropDialog();
    });


    $('#imageFileInput').change(
        function onImageSelected(event) {
            var input = this;

            if (!input.files || !input.files[0]) {
                return;
            }

            var reader = new FileReader();
            reader.onload = function (e) {
                dialogImage.attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);

            cropImageDataInput.val('');
            openCropDialog();
        }
    );
})(jQuery);