<?php

namespace App\Http\Controllers;

use File;
use Illuminate\Http\Request;
use App\User;
use App\Note;
use App\UserCheckedNote;
use App\Events\NoteEvent;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $user_id = $request->user_id;
        $user = User::find($user_id);
        $my_notes = $user->notes;

        $my_notes = Note::sortByDate($my_notes);

        $result = array();
        foreach($my_notes as $note){
            $user_checked_notes = $note->user_checked_notes;
            foreach ($user_checked_notes as $item) {
                $item->user;
            }
            $child = array(
                'note' => $note,
                'viewed_users' => $user_checked_notes
            );
            $result[] = $child;
        }

        /** Matching new notes and viewed notes*/
        $new_notes = [];
        $viewed_notes = [];
        $hidden_notes = [];
        if ($user_id == $request->user()->id) {
            $users = User::where('id', '!=' , $user_id)->orWhereNull('id')->get();
            foreach ($users as $_user) {
                $_notes = $_user->notes;
                foreach ($_notes as $item) {
                    $item->user;
                }
                $_notes = Note::sortByDate($_notes);


                $first = $_notes->first();
                if ($first) {
                    $_hidden = UserCheckedNote::where('note_id', $first->id)->where('user_id', $user_id)->where('hide', true)->count();
                    if ($_hidden > 0) {
                        $hidden_notes[] = $_notes->values();
                    } else {
                        $length = $_notes -> count();
                        $_length = 0;
                        foreach ($_notes as $_note) {
                            $exist = UserCheckedNote::where('note_id', $_note->id)->where('user_id', $user_id)->count();
                            if ($exist > 0) {
                                $_length++;
                            }
                        }
                        if ($length == $_length) {
                            $viewed_notes[] = $_notes->values();
                        } else {
                            $new_notes[] = $_notes->values();
                        }
                    }
                }
            }
        }
        return [
            'status' => true,
            'notes' => $result,
            'new_notes' => $new_notes,
            'viewed_notes' => $viewed_notes,
            'hidden_notes' => $hidden_notes,
            'user' => $user];
    }


    public function create()
    {


    }

    public function store(Request $request)
    {
        //

        $user = $request->user();
        $type = $request->type;
        $color = $request->color;
        $title = $request->title;
        $duration = $request->duration;

        $media_url = '';

        $storage_path = '';
        $file_name = '';
        if($type != 'default') {
            $file = $request->file('media');
            $extension = $file->getClientOriginalExtension();
            $storage_path = 'public/notes/'.$user->id;
            $file_name = rand() .'.'. $extension;
            $path = $file->storeAs($storage_path, $file_name);
            $media_url = str_replace('public', '', $path);
        }

        $note = new Note();
        $note->user_id = $user->id;
        $note->media_url = $media_url;
        $note->type = $type;
        $note->color = $color;
        $note->title = $title;
        $note->duration = $duration;
        $note->save();
        event(new NoteEvent($user, 'note.created'));
        return ['success' => true, 'note'=>$note];
    }

    public function show(Note $note)
    {

    }
    public function edit(Note $note)
    {

    }
    public function update(Request $request, Note $note)
    {

    }

    public function viewNote(Request $request)
    {
        $user = $request->user();
        $note_id = $request->note_id;
        $user_checked_note = $user->user_checked_notes()->where('note_id', $note_id)->first();
        if (!$user_checked_note) {
            $user_checked_note = new UserCheckedNote();
        }
        $user_checked_note->user_id = $user->id;
        $user_checked_note->note_id = $note_id;
        $user_checked_note->save();
        event(new NoteEvent($user, 'note.updated'));
        return ['success' => true];
    }

    public function hideShowNote(Request $request)
    {
        $user = $request->user();
        $note_id = $request->note_id;
        $value = $request->value;
//        return ['success' => true, 'note' => $request->all()];
        $user_checked_notes = $user->user_checked_notes()->where('note_id', $note_id)->get();
        foreach ($user_checked_notes as $user_checked_note) {
            if (!$user_checked_note) {
                $user_checked_note = new UserCheckedNote();
            }
            $user_checked_note->user_id = $user->id;
            $user_checked_note->note_id = $note_id;
            $user_checked_note->hide = $value;
            $user_checked_note->save();
        }

        return ['success' => true];
    }

    public function destroy(Request $request, Note $note)
    {
        $user = $request->user();

        /** Delete file if it exists*/
        if ($note->media_url) {
            $path = 'storage'.$note->media_url;
            if(File::exists($path)) {
                File::delete($path);
            }
        }

        $delete = $note->delete();
        event(new NoteEvent($user, 'note.updated'));
        $msg = $delete ? trans('msg.deleted') : trans('msg.not_deleted');
        return ['status' => $delete, 'msg' => $msg];
    }
}
