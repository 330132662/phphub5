<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Banner;
use App\Models\Link;
use App\Models\ActiveUser;
use App\Models\HotTopic;
use App\Models\Image;
use Illuminate\Http\Request;
use Auth;

class ActivityController extends Controller
{

    public function select(Request $request)
    {
        $judges = Judge::when($request->name, function ($query) use ($request) {
            return $query->where('name', 'like', '%'.$request->name.'%');
        })->when($request->item, function ($query) use ($request) {
            return $query->where('item', 'like', '%'.$request->item.'%');
        })->when($request->is_free, function ($query) use ($request) {
            return $query->where('is_free', 'like', '%'.$request->is_free.'%');
        })->paginate(10);
        return view('judges.index', compact('judges',$judges));
    }
	public function index(Request $request)
	{
        switch ($request->view) {
            case 'all':
                $activities = Activity::recent()->paginate(50);
                break;

            case 'mine':
                $activities = Auth::user()->activities();
                break;

            default:
                $activities = Auth::user()->subscribedActivityFeeds();
                break;
        }

        $links  = Link::allFromCache();
        $banners = Banner::allByPosition();

        $active_users = ActiveUser::fetchAll();
        $hot_topics = HotTopic::fetchAll();
        $images = Image::fromActivities($activities);

        return view('activities.index', compact('activities', 'links', 'banners', 'active_users', 'hot_topics', 'images'));
	}

}
