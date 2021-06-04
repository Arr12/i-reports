<section>
    <!-- Left Sidebar -->
    <aside id="leftsidebar" class="sidebar">
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
                                <button type="submit"><i class="material-icons">input</i>Sign Out</button>
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
                <li>
                    <a href="javascript:void(0);" class="menu-toggle">
                        <i class="material-icons">import_contacts</i>
                        <span>Lv.0 Daily Reports Global</span>
                    </a>
                    <ul class="ml-menu">
                        <li>
                            <a href="{{route('daily-report-global.ames')}}">
                                <span>Daily Report Ame</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{route('daily-report-global.annas')}}">
                                <span>Daily Report Anna</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{route('daily-report-global.carols')}}">
                                <span>Daily Report Carol</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{route('daily-report-global.erics')}}">
                                <span>Daily Report Eric</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{route('daily-report-global.ichas')}}">
                                <span>Daily Report Ichas</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{route('daily-report-global.lilies')}}">
                                <span>Daily Report Lily</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{route('daily-report-global.maydewis')}}">
                                <span>Daily Report Maydewi</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{route('daily-report-global.ranis')}}">
                                <span>Daily Report Rani</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:void(0);" class="menu-toggle">
                        <i class="material-icons">import_contacts</i>
                        <span>Lv.0 Daily Reports Indo</span>
                    </a>
                    <ul class="ml-menu">
                        <li>
                            <a href="{{route('daily-report-indo.icha-nurs')}}">
                                <span>Daily Report Indo Icha Nurs</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{route('daily-report-indo.irels')}}">
                                <span>Daily Report Indo Irels</span>
                            </a>
                        </li>
                    </ul>
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
                <li>
                    <a href="{{route('non-exclusive-report.non-exclusive')}}">
                        <i class="material-icons">import_contacts</i>
                        <span>Non Exclusive Report</span>
                    </a>
                </li>
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
