 <form name="upload-form" class="rx--upload-form" id="upload-form" method="post" enctype="multipart/form-data" data-rxfile="" data-email="">
                          @csrf
@method('POST')
                         
                          <input type="hidden" id="upload_preset" name="attachment" value="himofcxn">
                          <input class="rx--upload" type="file" name="attachment" id="fileUploads" accept="image/png, image/jpeg, image/gif, application/pdf">
                          <label for="fileUploads" class="btn btn--primary upload-button">Upload</label>
                        </form>