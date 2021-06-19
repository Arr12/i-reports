@php
    $role = auth()->user()->role_custom;
@endphp
<section>
    <!-- Left Sidebar -->
    <aside id="leftsidebar" class="sidebar desktop-toggle-hide">
        <!-- User Info -->
        <div class="user-info">
            <div class="image">
                <img src="/images/user.png" width="48" height="48" alt="User" />
            </div>
            <div class="info-container">
                <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{Auth::user()->name}}</div>
                <div class="email">{{Auth::user()->email}}</div>
                <div class="btn-group user-helper-dropdown">
                    <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>
                    <ul class="dropdown-menu pull-right">
                        {{-- <li><a href="javascript:void(0);"><i class="material-icons">person</i>Profile</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="javascript:void(0);"><i class="material-icons">group</i>Followers</a></li>
                        <li><a href="javascript:void(0);"><i class="material-icons">shopping_cart</i>Sales</a></li>
                        <li><a href="javascript:void(0);"><i class="material-icons">favorite</i>Likes</a></li>
                        <li role="separator" class="divider"></li> --}}
                        <li>
                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="sign-out" type="submit"><i class="material-icons">input</i>Sign Out</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- #User Info -->
        <!-- Menu -->
        <div class="menu">
            <ul class="list">
                <li class="header">MAIN NAVIGATION</li>
                <li>
                    <a href="/">
                        <i class="material-icons">home</i>
                        <span>Home</span>
                    </a>
                </li>
                @if ($role == 'Officer Global Ame' || $role == 'Officer Global Anna' || $role == 'Officer Global Carol' || $role == 'Officer Global Eric' || $role == 'Officer Global Icha' || $role == 'Officer Global Lily' || $role == 'Offiecer Global Maydewi' || $role == 'Officer Global Rani' || $role == 'Supervisor' || $role == 'Administrator')
                <li>
                    <a href="javascript:void(0);" class="menu-toggle">
                        <i class="material-icons">import_contacts</i>
                        <span>Lv.0 Daily Reports Global</span>
                    </a>
                    <ul class="ml-menu">
                        @if($role == 'Officer Global Ame' || $role == 'Supervisor' || $role == 'Administrator')
                        <li>
                            <a href="{{route('daily-report-global.ames')}}">
                                <span>Daily Report Ame</span>
                            </a>
                        </li>
                        @endif
                        @if ($role == 'Officer Global Anna' || $role == 'Supervisor' || $role == 'Administrator')
                        <li>
                            <a href="{{route('daily-report-global.annas')}}">
                                <span>Daily Report Anna</span>
                            </a>
                        </li>
                        @endif
                        @if ($role == 'Officer Global Carol' || $role == 'Supervisor' || $role == 'Administrator')
                        <li>
                            <a href="{{route('daily-report-global.carols')}}">
                                <span>Daily Report Carol</span>
                            </a>
                        </li>
                        @endif
                        @if ($role == 'Officer Global Eric' || $role == 'Supervisor' || $role == 'Administrator')
                        <li>
                            <a href="{{route('daily-report-global.erics')}}">
                                <span>Daily Report Eric</span>
                            </a>
                        </li>
                        @endif
                        @if ($role == 'Officer Global Icha' || $role == 'Supervisor' || $role == 'Administrator')
                        <li>
                            <a href="{{route('daily-report-global.ichas')}}">
                                <span>Daily Report Icha</span>
                            </a>
                        </li>
                        @endif
                        @if ($role == 'Officer Global Lily' || $role == 'Supervisor' || $role == 'Administrator')
                        <li>
                            <a href="{{route('daily-report-global.lilies')}}">
                                <span>Daily Report Lily</span>
                            </a>
                        </li>
                        @endif
                        @if ($role == 'Officer Global Maydewi' || $role == 'Supervisor' || $role == 'Administrator')
                        <li>
                            <a href="{{route('daily-report-global.maydewis')}}">
                                <span>Daily Report Maydewi</span>
                            </a>
                        </li>
                        @endif
                        @if ($role == 'Officer Global Rani' || $role == 'Supervisor' || $role == 'Administrator')
                        <li>
                            <a href="{{route('daily-report-global.ranis')}}">
                                <span>Daily Report Rani</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if ($role == 'Officer Indo Irel' || $role == 'Officer Indo Ichanur' || $role == 'Supervisor' || $role == 'Administrator')
                <li>
                    <a href="javascript:void(0);" class="menu-toggle">
                        <i class="material-icons">import_contacts</i>
                        <span>Lv.0 Daily Reports Indo</span>
                    </a>
                    <ul class="ml-menu">
                        @if ($role == 'Officer Indo Ichanur' || $role == 'Supervisor' || $role == 'Administrator')
                        <li>
                            <a href="{{route('daily-report-indo.icha-nurs')}}">
                                <span>Daily Report Indo Icha Nurs</span>
                            </a>
                        </li>
                        @endif
                        @if ($role == 'Officer Indo Irel' || $role == 'Supervisor' || $role == 'Administrator')
                        <li>
                            <a href="{{route('daily-report-indo.irels')}}">
                                <span>Daily Report Indo Irels</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if ($role == 'Administrator' || $role == 'Supervisor')
                <li>
                    <a href="{{route('daily-report-marker.marker')}}">
                        <i class="material-icons">import_contacts</i>
                        <span>Daily Report Marker 7</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);" class="menu-toggle">
                        <i class="material-icons">import_contacts</i>
                        <span>Lv.0 Spam</span>
                    </a>
                    <ul class="ml-menu">
                        <li>
                            <a href="{{route('report-spam.spam-mangatoon')}}">
                                <span>Mangatoon</span>
                            </a>
                        </li>
                        {{--
                        <li>
                            <a href="{{route('report-spam.spam-royalroad')}}">
                                <span>RoyalRoad</span>
                            </a>
                        </li>
                        --}}
                        <li>
                            <a href="{{route('report-spam.spam-wn-uncontracted')}}">
                                <span>WN Uncontracted</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{route('report-spam.spam-novel-list-from-ranking')}}">
                                <span>Novel List From Ranking</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                @if ($role == 'Officer Global Ame' || $role == 'Officer Global Anna' || $role == 'Officer Global Carol' || $role == 'Officer Global Eric' || $role == 'Officer Global Icha' || $role == 'Officer Global Lily' || $role == 'Officer Global Maydewi' || $role == 'Officer Global Rani' || $role == 'Supervisor' || $role == 'Administrator')
                <li>
                    <a href="{{route('non-exclusive-report.non-exclusive')}}">
                        <i class="material-icons">import_contacts</i>
                        <span>Non Exclusive Report</span>
                    </a>
                </li>
                @endif
                @if ($role == 'Supervisor' || $role == 'Administrator')
                <li>
                    <a href="javascript:void(0);" class="menu-toggle">
                        <i class="material-icons">import_contacts</i>
                        <span>Lv.1 Team Monitoring</span>
                    </a>
                    <ul class="ml-menu">
                        <li>
                            <a href="{{route('team-monitoring.global')}}">
                                <span>Global Team Monitoring</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{route('team-monitoring.indo')}}">
                                <span>Indo Team Monitoring</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:void(0);" class="menu-toggle">
                        <i class="material-icons">import_contacts</i>
                        <span>Lv.2 All Reports</span>
                    </a>
                    <ul class="ml-menu">
                        <li>
                            <a href="{{route('all-report.weekly')}}">
                                <span>Weekly Report</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{route('all-report.monthly')}}">
                                <span>Monthly Report</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="{{route('report-to-sunny.report-to-sunny')}}">
                        <i class="material-icons">import_contacts</i>
                        <span>Report to Sunny</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>
        <!-- #Menu -->
        <!-- Footer -->
        <div class="legal">
            <div class="copyright">
                &copy; 2021 <a href="javascript:void(0);">All rights reserved</a>.
            </div>
            <div class="version">
                <b>Version: </b> 1.0.0
            </div>
        </div>
        <!-- #Footer -->
    </aside>
    <!-- #END# Left Sidebar -->
</section>
