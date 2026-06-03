<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;

class EnsureSecurityAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->user_type === 'security') {
            // Load permissions
            $permissions = [];
            $filePath = 'security_permissions.json';
            if (Storage::exists($filePath)) {
                $permissions = json_decode(Storage::get($filePath), true);
            } else {
                // Fallback default permissions
                $permissions = [
                    'dashboard'       => true,
                    'visitors'        => true,
                    'verifications'   => false,
                    'duty_roster'     => true,
                    'services'        => false,
                    'culture'         => false,
                    'events'          => false,
                    'messages'        => false,
                    'facilities'      => false,
                    'road_notices'    => true,
                    'announcements'   => true,
                    'forum'           => false,
                    'emergencies'     => true,
                ];
            }

            // Map current admin route to permission key
            $routeName = $request->route()?->getName();
            
            $routeMap = [
                'admin.dashboard'                => 'dashboard',
                'admin.visitors.create'          => 'visitors',
                'admin.verifications'            => 'verifications',
                'admin.security-duties'          => 'duty_roster',
                'admin.services-management'      => 'services',
                'admin.culture-management'       => 'culture',
                'admin.events-management'        => 'events',
                'admin.contact-messages'         => 'messages',
                'admin.facilities'               => 'facilities',
                'admin.road-notices'             => 'road_notices',
                'admin.announcements-management' => 'announcements',
                'admin.forum-management'         => 'forum',
                'admin.emergencies-management'   => 'emergencies',
            ];

            if ($routeName === 'admin.security-permissions') {
                abort(403, 'Unauthorized access. Only community administrators can access this module.');
            }

            if ($routeName && isset($routeMap[$routeName])) {
                $permKey = $routeMap[$routeName];
                if (!($permissions[$permKey] ?? false)) {
                    abort(403, 'Unauthorized access. The community administrator has disabled this admin module for the Security Guard role.');
                }
            }
        }

        return $next($request);
    }
}
