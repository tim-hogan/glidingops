@extends('layouts.app')

@push('styles')
@endpush

@push('scripts')
<!-- reference Moment.js library -->
<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.7.0/moment.min.js" type="text/javascript"></script>
<script>
$('.custom-file-input').on('change',function(){
  var fileName = $(this)[0].files[0].name
  $(this).next('.custom-file-label').html(fileName)
})
$(function () {
    // guess user timezone 
    $('#tz').val(moment().format('Z'))
})

$(document).ready(function() {
  $(".select2").select2({
    tags: true
  })
  $("#documentType").change(function(event) {
    const needExpDate = [
      'BFR',
      'ICR'
    ]
    const newValue = event.target.value
    $('#documentExpiresAt').prop('required', needExpDate.includes(newValue))
  })

  $("form.app-remove-document").submit(function(e){
    return confirm('Do you really want to delete this version of the document?')
  });
})

</script>
@endpush

@section('content')
  <div class='container'>
    <!-- Errors -->
    @if ($errors->any())
    <div class="row alert alert-danger">
      <div class="col-sm-12">
        <!-- if there are creation/update errors, they will show here -->
        {{ Html::ul($errors->all()) }}
      </div>
    </div>
    @endif

    <!-- Main form -->
    <div class="row">
      <div class="col-sm-12">
        <h2>{{ ($method === 'put') ? "{$model->displayname}" : 'Create Member' }}</h2>
      </div>
    </div>
    <!-- Documents form -->
    {{ Form::model($newDocument, ['route' => ['members.documents.store', $model->id], 'method' => 'post', 'files' => true]) }}
      <input type="hidden" name="tz" id="tz">
      <div class="form-row">
        <div class="form-group col-md-5">
          <label for="documentFile">File</label>
          <div class="custom-file">
            <input type="file" class="custom-file-input" id="documentFile" name="documentFile" required>
            <label class="custom-file-label" for="documentFile" aria-describedby="documentFileHelp">Choose a file ...</label>
          </div>
          <small id="documentFileHelp" class="form-text text-muted">Select a document to be uploaded.</small>
        </div>
        <div class="form-group col-md-3">
          <label for="documentType">Document type</label>
          <select class="select2 form-control" id="documentType" name="documentType" required>
            <option value=''>Please choose ...</option>
            @foreach (App\Models\Document::documentCollections() as $collectionName)
            <option>{{ $collectionName }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group col-md-4">
          <label for="documentExpiresAt">Expiry date</label>
          <input type="date" class="form-control" id="documentExpiresAt" name="documentExpiresAt">
        </div>
      </div>
      <button type="submit" class="btn btn-primary">Add</button>
    {{ Form::close() }}
    <div class="row">
      <div class="col-sm-12"><h3>Documents</h3></div>
    </div>
    <table class="table table-sm">
      <thead>
        <tr>
          <th>Name</th>
          <th>Type</th>
          <th>Expiry date</th>
          <th>Version count</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($model->latestDocuments()->get() as $document)
          <tr class='{{ $document->isExpired() ? "bg-danger" : "" }}'>
            <td>{{ $document->file_name }}</td>
            <td>{{ $document->collection_name }}</td>
            <td>{{ ($document->expires_at === null) ? '' : $document->expires_at->format('d/m/Y') }}</td>
            <td>{{ $document->version_count }}</td>
            <td class="app-actions">
              <a class="btn" href="{{ route('members.documents.show', $parameters = [$model->id, $document->id]) }}" target="_blank">
                <span class="fas fa-download" data-toggle="tooltip" title="Download the latest version of the document"/>
              </a>
              {{ Form::model($model, ['route' => ['members.documents.collections.latest', $model->id, $document->collection_name], 
                                      'method' => 'delete',
                                      'class' => 'app-form-action, app-remove-document']) }}
              <button type="submit" class="btn fas fa-trash-alt" data-toggle="tooltip" title="Remove the latest version of the document"/>
              {{ Form::close() }}
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>  
@endsection