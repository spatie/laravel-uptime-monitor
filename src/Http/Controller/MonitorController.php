<?php
namespace Spatie\UptimeMonitor\Http\Controller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\UptimeMonitor\Models\Monitor;

class MonitorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Monitor::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, config('laravel-uptime-monitor.restAPI.validationRules'));
        $url = Url::fromString($request->get('url'));
        $monitor = Monitor::create([
            'url' => trim($url, '/'),
            'look_for_string' => $request->get('look_for_string') ?? '',
            'uptime_check_method' => $request->has('look_for_string') ? 'get' : 'head',
            'certificate_check_enabled' => $url->getScheme() === 'https',
            'uptime_check_interval_in_minutes' => config('laravel-uptime-monitor.uptime_check.run_interval_in_minutes'),
        ]);
        return response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Monitor::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, config('laravel-uptime-monitor.restAPI.validationRules'));

        $monitor = Monitor::findOrFail($id);
        $look_for_string = ($request->has('look_for_string')) ? $request->get('look_for_string') : $monitor->look_for_string;
        return $monitor->update(['url' => $request->get('url'), 'look_for_string' => $look_for_string]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $monitor = Monitor::findOrFail($id);
        return $monitor->delete();
    }
}