<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CustomMediaController extends Controller
{

    /** @var string */
    private $filesystem;

    public function __construct()
    {
        $this->filesystem = config('voyager.storage.disk');
    }

    public function delete(Request $request)
    {
        
        // Check permission
        $this->authorize('browse_media');
      
        $files = $request['files'];
        $success = true;
        $error = '';
        if(!Storage::disk($this->filesystem)->delete('/'.$files)) {
            $error = __('voyager::media.error_deleting_file');
            $success = false;
        }
        return compact('success', 'error');
    }
}
