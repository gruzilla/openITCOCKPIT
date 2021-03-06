<?php
// Copyright (C) <2015>  <it-novum GmbH>
//
// This file is dual licensed
//
// 1.
//	This program is free software: you can redistribute it and/or modify
//	it under the terms of the GNU General Public License as published by
//	the Free Software Foundation, version 3 of the License.
//
//	This program is distributed in the hope that it will be useful,
//	but WITHOUT ANY WARRANTY; without even the implied warranty of
//	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//	GNU General Public License for more details.
//
//	You should have received a copy of the GNU General Public License
//	along with this program.  If not, see <http://www.gnu.org/licenses/>.
//

// 2.
//	If you purchased an openITCOCKPIT Enterprise Edition you can use this file
//	under the terms of the openITCOCKPIT Enterprise Edition license agreement.
//	License agreement and license key will be shipped with the order
//	confirmation.

/**
 * @property \itnovum\openITCOCKPIT\Monitoring\QueryHandler $QueryHandler
 */

use itnovum\openITCOCKPIT\Core\HostSharingPermissions;
use itnovum\openITCOCKPIT\Core\Views\Host;
use itnovum\openITCOCKPIT\Core\Hoststatus;
use itnovum\openITCOCKPIT\Core\Views\Hosttemplate;

$this->Paginator->options(['url' => $this->params['named']]);
?>
<div class="row">
    <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
        <h1 class="page-title txt-color-blueDark">
            <i class="fa fa-desktop fa-fw "></i>
            <?php echo __('Hosts') ?>
            <span>>
                <?php echo __('Overview'); ?>
            </span>
        </h1>
    </div>
</div>

<?php echo $this->Flash->render('positive'); ?>

<div id="error_msg"></div>

<div class="alert alert-success alert-block" id="flashSuccess" style="display:none;">
    <a href="#" data-dismiss="alert" class="close">×</a>
    <h4 class="alert-heading"><i class="fa fa-check-circle-o"></i> <?php echo __('Command sent successfully'); ?></h4>
    <?php echo __('Page refresh in'); ?> <span id="autoRefreshCounter"></span> <?php echo __('seconds...'); ?>
</div>

<?php if (!$QueryHandler->exists()): ?>
    <div class="alert alert-danger alert-block">
        <a href="#" data-dismiss="alert" class="close">×</a>
        <h4 class="alert-heading"><i class="fa fa-warning"></i> <?php echo __('Monitoring Engine is not running!'); ?>
        </h4>
        <?php echo __('File %s does not exists', $QueryHandler->getPath()); ?>
    </div>
<?php endif; ?>

<massdelete></massdelete>
<massdeactivate></massdeactivate>

<section id="widget-grid" class="">

    <div class="row">
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="jarviswidget jarviswidget-color-blueDark" id="wid-id-1" data-widget-editbutton="false">
                <header>
                    <div class="widget-toolbar" role="menu">
                        <button type="button" class="btn btn-xs btn-default" ng-click="load()">
                            <i class="fa fa-refresh"></i>
                            <?php echo __('Refresh'); ?>
                        </button>

                        <?php if ($this->Acl->hasPermission('add')): ?>
                            <a href="/hosts/add" class="btn btn-xs btn-success">
                                <i class="fa fa-plus"></i>
                                <?php echo __('New'); ?>
                            </a>
                        <?php endif; ?>
                        <button type="button" class="btn btn-xs btn-primary" ng-click="triggerFilter()">
                            <i class="fa fa-filter"></i>
                            <?php echo __('Filter'); ?>
                        </button>
                    </div>
                    <div class="jarviswidget-ctrls" role="menu"></div>

                    <span class="widget-icon hidden-mobile"> <i class="fa fa-desktop"></i> </span>
                    <h2 class="hidden-mobile"><?php echo __('Hosts'); ?></h2>
                    <ul class="nav nav-tabs pull-right" id="widget-tab-1">
                        <li class="active">
                            <a href="<?php echo Router::url(array_merge(['controller' => 'hosts', 'action' => 'index'], $this->params['named'])); ?>"><i
                                        class="fa fa-stethoscope"></i> <span
                                        class="hidden-mobile hidden-tablet"> <?php echo __('Monitored'); ?> </span> </a>
                        </li>
                        <?php if ($this->Acl->hasPermission('notMonitored')): ?>
                            <li class="">
                                <a href="<?php echo Router::url(array_merge(['controller' => 'hosts', 'action' => 'notMonitored'], $this->params['named'])); ?>"><i
                                            class="fa fa-user-md"></i> <span
                                            class="hidden-mobile hidden-tablet"> <?php echo __('Not monitored'); ?> </span></a>
                            </li>
                        <?php endif; ?>
                        <?php if ($this->Acl->hasPermission('disabled')): ?>
                            <li>
                                <a href="<?php echo Router::url(array_merge(['controller' => 'hosts', 'action' => 'disabled'], $this->params['named'])); ?>"><i
                                            class="fa fa-power-off"></i> <span
                                            class="hidden-mobile hidden-tablet"> <?php echo __('Disabled'); ?> </span></a>
                            </li>
                        <?php endif; ?>
                        <?php if ($this->Acl->hasPermission('index', 'DeletedHosts')): ?>
                            <li>
                                <a href="<?php echo Router::url(array_merge(['controller' => 'deleted_hosts', 'action' => 'index'], $this->params['named'])); ?>"><i
                                            class="fa fa-trash-o"></i> <span
                                            class="hidden-mobile hidden-tablet"> <?php echo __('Deleted'); ?> </span></a>
                            </li>
                        <?php endif; ?>
                    </ul>

                </header>
                <div>


                    <div class="widget-body no-padding">

                        <div class="list-filter well" ng-show="showFilter">
                            <h3><i class="fa fa-filter"></i> <?php echo __('Filter'); ?></h3>
                            <div class="row">
                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group smart-form">
                                        <label class="input"> <i class="icon-prepend fa fa-desktop"></i>
                                            <input type="text" class="input-sm"
                                                   placeholder="<?php echo __('Filter by host name'); ?>"
                                                   ng-model="filter.Host.name"
                                                   ng-model-options="{debounce: 500}">
                                        </label>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group smart-form">
                                        <label class="input"> <i class="icon-prepend fa fa-filter"></i>
                                            <input type="text" class="input-sm"
                                                   placeholder="<?php echo __('Filter by IP address'); ?>"
                                                   ng-model="filter.Host.address"
                                                   ng-model-options="{debounce: 500}">
                                        </label>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group smart-form">
                                        <label class="input"> <i class="icon-prepend fa fa-filter"></i>
                                            <input type="text" class="input-sm"
                                                   placeholder="<?php echo __('Filter by output'); ?>"
                                                   ng-model="filter.Hoststatus.output"
                                                   ng-model-options="{debounce: 500}">
                                        </label>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group smart-form">
                                        <i class="icon-prepend fa fa-filter"></i>
                                        <input type="text" class="input-sm"
                                               data-role="tagsinput"
                                               placeholder="<?php echo __('Filter by tags'); ?>"
                                               ng-model="filter.Host.keywords"
                                               ng-model-options="{debounce: 500}">
                                    </div>
                                </div>

                            </div>
                            <div class="row">

                                <div class="col-xs-12 col-md-3">
                                    <fieldset>
                                        <legend><?php echo __('Host status'); ?></legend>
                                        <div class="form-group smart-form">
                                            <label class="checkbox small-checkbox-label">
                                                <input type="checkbox" name="checkbox" checked="checked"
                                                       ng-model="filter.Hoststatus.current_state.up"
                                                       ng-model-options="{debounce: 500}">
                                                <i class="checkbox-success"></i>
                                                <?php echo __('Up'); ?>
                                            </label>


                                            <label class="checkbox small-checkbox-label">
                                                <input type="checkbox" name="checkbox" checked="checked"
                                                       ng-model="filter.Hoststatus.current_state.down"
                                                       ng-model-options="{debounce: 500}">
                                                <i class="checkbox-danger"></i>
                                                <?php echo __('Down'); ?>
                                            </label>

                                            <label class="checkbox small-checkbox-label">
                                                <input type="checkbox" name="checkbox" checked="checked"
                                                       ng-model="filter.Hoststatus.current_state.unreachable"
                                                       ng-model-options="{debounce: 500}">
                                                <i class="checkbox-default"></i>
                                                <?php echo __('Unreachable'); ?>
                                            </label>
                                        </div>
                                    </fieldset>
                                </div>


                                <div class="col-xs-12 col-md-3">
                                    <fieldset>
                                        <legend><?php echo __('Acknowledgements'); ?></legend>
                                        <div class="form-group smart-form">
                                            <label class="checkbox small-checkbox-label">
                                                <input type="checkbox" name="checkbox" checked="checked"
                                                       ng-model="filter.Hoststatus.acknowledged"
                                                       ng-model-options="{debounce: 500}">
                                                <i class="checkbox-primary"></i>
                                                <?php echo __('Acknowledge'); ?>
                                            </label>

                                            <label class="checkbox small-checkbox-label">
                                                <input type="checkbox" name="checkbox" checked="checked"
                                                       ng-model="filter.Hoststatus.not_acknowledged"
                                                       ng-model-options="{debounce: 500}">
                                                <i class="checkbox-primary"></i>
                                                <?php echo __('Not acknowledged'); ?>
                                            </label>
                                        </div>
                                    </fieldset>
                                </div>

                                <div class="col-xs-12 col-md-3">
                                    <fieldset>
                                        <legend><?php echo __('Downtimes'); ?></legend>
                                        <div class="form-group smart-form">
                                            <label class="checkbox small-checkbox-label">
                                                <input type="checkbox" name="checkbox" checked="checked"
                                                       ng-model="filter.Hoststatus.in_downtime"
                                                       ng-model-options="{debounce: 500}">
                                                <i class="checkbox-primary"></i>
                                                <?php echo __('In downtime'); ?>
                                            </label>

                                            <label class="checkbox small-checkbox-label">
                                                <input type="checkbox" name="checkbox" checked="checked"
                                                       ng-model="filter.Hoststatus.not_in_downtime"
                                                       ng-model-options="{debounce: 500}">
                                                <i class="checkbox-primary"></i>
                                                <?php echo __('Not in downtime'); ?>
                                            </label>
                                        </div>
                                    </fieldset>
                                </div>

                                <?php if (sizeof($satellites) > 1): ?>
                                    <div class="col-xs-12 col-md-3">
                                        <fieldset>
                                            <legend><?php echo __('Instance'); ?></legend>
                                            <div class="form-group smart-form">
                                                <select
                                                        id="Instance"
                                                        data-placeholder="<?php echo __('Filter by instance'); ?>"
                                                        class="form-control"
                                                        chosen="{}"
                                                        multiple
                                                        ng-model="filter.Host.satellite_id"
                                                        ng-model-options="{debounce: 500}">
                                                    <?php
                                                    foreach ($satellites as $satelliteId => $satelliteName):
                                                        printf('<option value="%s">%s</option>', h($satelliteId), h($satelliteName));
                                                    endforeach;
                                                    ?>
                                                </select>
                                            </div>
                                        </fieldset>
                                    </div>
                                <?php endif; ?>

                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="pull-right margin-top-10">
                                        <button type="button" ng-click="resetFilter()"
                                                class="btn btn-xs btn-danger">
                                            <?php echo __('Reset Filter'); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="mobile_table">
                            <table id="host_list"
                                   class="table table-striped table-hover table-bordered smart-form"
                                   style="">
                                <thead>
                                <tr>
                                    <th colspan="2" class="no-sort width-90" ng-click="orderBy('Hoststatus.current_state')">
                                        <i class="fa" ng-class="getSortClass('Hoststatus.current_state')"></i>
                                        <?php echo __('Hoststatus'); ?>
                                    </th>

                                    <th class="no-sort text-center">
                                        <i class="fa fa-user fa-lg" title="<?php echo __('is acknowledged'); ?>"></i>
                                    </th>

                                    <th class="no-sort text-center">
                                        <i class="fa fa-power-off fa-lg"
                                           title="<?php echo __('is in downtime'); ?>"></i>
                                    </th>

                                    <th class="no-sort text-center">
                                        <i class="fa fa fa-area-chart fa-lg" title="<?php echo __('Grapher'); ?>"></i>
                                    </th>

                                    <th class="no-sort text-center">
                                        <i title="<?php echo __('Shared'); ?>" class="fa fa-sitemap fa-lg"></i>
                                    </th>

                                    <th class="no-sort text-center">
                                        <strong title="<?php echo __('Passively transferred host'); ?>">P</strong>
                                    </th>

                                    <th class="no-sort" ng-click="orderBy('Host.name')">
                                        <i class="fa" ng-class="getSortClass('Host.name')"></i>
                                        <?php echo __('Host name'); ?>
                                    </th>

                                    <th class="no-sort" ng-click="orderBy('Host.address')">
                                        <i class="fa" ng-class="getSortClass('Host.address')"></i>
                                        <?php echo __('IP address'); ?>
                                    </th>

                                    <th class="no-sort tableStatewidth"
                                        ng-click="orderBy('Hoststatus.last_state_change')">
                                        <i class="fa" ng-class="getSortClass('Hoststatus.last_state_change')"></i>
                                        <?php echo __('Last state change'); ?>
                                    </th>

                                    <th class="no-sort tableStatewidth" ng-click="orderBy('Hoststatus.last_check')">
                                        <i class="fa" ng-class="getSortClass('Hoststatus.last_check')"></i>
                                        <?php echo __('Last check'); ?>
                                    </th>

                                    <th class="no-sort" ng-click="orderBy('Hoststatus.output')">
                                        <i class="fa" ng-class="getSortClass('Hoststatus.output')"></i>
                                        <?php echo __('Host output'); ?>
                                    </th>

                                    <th class="no-sort" ng-click="orderBy('Host.satellite_id')">
                                        <i class="fa" ng-class="getSortClass('Host.satellite_id')"></i>
                                        <?php echo __('Instance'); ?>
                                    </th>

                                    <th class="no-sort text-center editItemWidth"><i class="fa fa-gear fa-lg"></i></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr ng-repeat="host in hosts">
                                    <td class="width-5">
                                        <input type="checkbox"
                                               ng-model="massChange[host.Host.id]"
                                               ng-show="host.Host.allow_edit">
                                    </td>

                                    <td class="text-center">
                                        <hoststatusicon host="host"></hoststatusicon>
                                    </td>

                                    <td class="text-center">
                                        <i class="fa fa-lg fa-user"
                                           ng-show="host.Hoststatus.problemHasBeenAcknowledged"
                                           ng-if="host.Hoststatus.acknowledgement_type == 1"></i>

                                        <i class="fa fa-lg fa-user-o"
                                           ng-show="host.Hoststatus.problemHasBeenAcknowledged"
                                           ng-if="host.Hoststatus.acknowledgement_type == 2"
                                           title="<?php echo __('Sticky Acknowledgedment'); ?>"></i>
                                    </td>

                                    <td class="text-center">
                                        <i class="fa fa-lg fa-power-off"
                                           ng-show="host.Hoststatus.scheduledDowntimeDepth > 0"></i>
                                    </td>

                                    <td class="text-center">
                                        <?php if ($this->Acl->hasPermission('serviceList', 'services')): ?>
                                            <a href="/services/serviceList/{{ host.Host.id }}"
                                               class="txt-color-blueDark"
                                               ng-if="host.Host.has_graphs">
                                                <i class="fa fa-lg fa-area-chart"></i>
                                            </a>
                                        <?php else: ?>
                                            <i class="fa fa-lg fa-area-chart txt-color-blueDark"
                                               ng-if="host.Host.has_graphs">
                                            </i>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center">

                                        <a class="txt-color-blueDark" title="<?php echo __('Shared'); ?>"
                                           ng-if="host.Host.allow_sharing === true && host.Host.containerIds.length > 1"
                                           href="/hosts/sharing/{{ host.Host.id }}">
                                            <i class="fa fa-sitemap fa-lg "></i></a>

                                        <i class="fa fa-low-vision fa-lg txt-color-blueLight"
                                           ng-if="host.Host.allow_sharing === false && host.Host.containerIds.length > 1"
                                           title="<?php echo __('Restricted view'); ?>"></i>
                                    </td>

                                    <td class="text-center">
                                        <strong title="<?php echo __('Passively transferred host'); ?>"
                                                ng-show="host.Host.active_checks_enabled === false || host.Host.is_satellite_host === true">
                                            P
                                        </strong>
                                    </td>

                                    <td>
                                        <?php if ($this->Acl->hasPermission('browser')): ?>
                                            <a href="/hosts/browser/{{ host.Host.id }}">
                                                {{ host.Host.hostname }}
                                            </a>
                                        <?php else: ?>
                                            {{ host.Host.hostname }}
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        {{ host.Host.address }}
                                    </td>

                                    <td>
                                        {{ host.Hoststatus.last_state_change }}
                                    </td>

                                    <td>
                                        {{ host.Hoststatus.lastCheck }}
                                    </td>

                                    <td>
                                        {{ host.Hoststatus.output }}
                                    </td>

                                    <td>
                                        {{ host.Host.satelliteName }}
                                    </td>

                                    <td class="width-50">
                                        <div class="btn-group">
                                            <?php if ($this->Acl->hasPermission('edit')): ?>
                                                <a href="/hosts/edit/{{host.Host.id}}"
                                                   ng-if="host.Host.allow_edit"
                                                   class="btn btn-default">
                                                    &nbsp;<i class="fa fa-cog"></i>&nbsp;
                                                </a>
                                            <?php else: ?>
                                                <a href="javascript:void(0);" class="btn btn-default">
                                                    &nbsp;<i class="fa fa-cog"></i>&nbsp;</a>
                                            <?php endif; ?>
                                            <a href="javascript:void(0);" data-toggle="dropdown"
                                               class="btn btn-default dropdown-toggle"><span
                                                        class="caret"></span></a>
                                            <ul class="dropdown-menu pull-right" id="menuHack-{{host.Host.uuid}}">
                                                <?php if ($this->Acl->hasPermission('edit')): ?>
                                                    <li ng-if="host.Host.allow_edit">
                                                        <a href="/hosts/edit/{{host.Host.id}}">
                                                            <i class="fa fa-cog"></i> <?php echo __('Edit'); ?>
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                                <?php if ($this->Acl->hasPermission('sharing')): ?>
                                                    <li ng-if="host.Host.allow_sharing">
                                                        <a href="/hosts/sharing/{{host.Host.id}}">
                                                            <i class="fa fa-sitemap fa-rotate-270"></i>
                                                            <?php echo __('Sharing'); ?>
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                                <?php if ($this->Acl->hasPermission('deactivate')): ?>
                                                    <li ng-if="host.Host.allow_edit">
                                                        <a href="javascript:void(0);"
                                                           ng-click="confirmDeactivate(getObjectForDelete(host))">
                                                            <i class="fa fa-plug"></i> <?php echo __('Disable'); ?>
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                                <?php if ($this->Acl->hasPermission('serviceList', 'services')): ?>
                                                    <li>
                                                        <a href="/services/serviceList/{{host.Host.id}}">
                                                            <i class="fa fa-list"></i> <?php echo __('Service List'); ?>
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                                <?php if ($this->Acl->hasPermission('allocateToHost', 'servicetemplategroups')): ?>
                                                    <li>
                                                        <a href="/hosts/allocateServiceTemplateGroup/{{host.Host.id}}">
                                                            <i class="fa fa-external-link"></i>
                                                            <?php echo __('Allocate Service Template Group'); ?>
                                                        </a>
                                                    </li>
                                                <?php endif; ?>

                                                <?php if ($this->Acl->hasPermission('edit')): ?>
                                                    <li ng-if="host.Host.allow_edit">
                                                        <?php echo $this->AdditionalLinks->renderAsListItems(
                                                            $additionalLinksList,
                                                            '{{host.Host.id}}',
                                                            [],
                                                            true
                                                        ); ?>
                                                    </li>
                                                <?php endif; ?>
                                                <?php if ($this->Acl->hasPermission('delete')): ?>
                                                    <li class="divider" ng-if="host.Host.allow_edit"></li>
                                                    <li ng-if="host.Host.allow_edit">
                                                        <a href="javascript:void(0);" class="txt-color-red"
                                                           ng-click="confirmDelete(getObjectForDelete(host))">
                                                            <i class="fa fa-trash-o"></i> <?php echo __('Delete'); ?>
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </td>

                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row margin-top-10 margin-bottom-10">
                            <div class="row margin-top-10 margin-bottom-10" ng-show="hosts.length == 0">
                                <div class="col-xs-12 text-center txt-color-red italic">
                                    <?php echo __('No entries match the selection'); ?>
                                </div>
                            </div>
                        </div>


                        <div class="row margin-top-10 margin-bottom-10">
                            <div class="col-xs-12 col-md-2 text-muted text-center">
                                <span ng-show="selectedElements > 0">({{selectedElements}})</span>
                            </div>
                            <div class="col-xs-12 col-md-2">
                                <span ng-click="selectAll()" class="pointer">
                                    <i class="fa fa-lg fa-check-square-o"></i>
                                    <?php echo __('Select all'); ?>
                                </span>
                            </div>
                            <div class="col-xs-12 col-md-2">
                                <span ng-click="undoSelection()" class="pointer">
                                    <i class="fa fa-lg fa-square-o"></i>
                                    <?php echo __('Undo selection'); ?>
                                </span>
                            </div>
                            <div class="col-xs-12 col-md-2">
                                <a ng-href="{{ linkForCopy() }}" class="a-clean">
                                    <i class="fa fa-lg fa-files-o"></i>
                                    <?php echo __('Copy'); ?>
                                </a>
                            </div>
                            <div class="col-xs-12 col-md-2 txt-color-red">
                                <span ng-click="confirmDelete(getObjectsForDelete())" class="pointer">
                                    <i class="fa fa-lg fa-trash-o"></i>
                                    <?php echo __('Delete'); ?>
                                </span>
                            </div>
                            <div class="col-xs-12 col-md-2">
                                <div class="btn-group">
                                    <a href="javascript:void(0);" class="btn btn-default"><?php echo __('More'); ?></a>
                                    <a href="javascript:void(0);" data-toggle="dropdown"
                                       class="btn btn-default dropdown-toggle"><span
                                                class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <?php if ($this->Acl->hasPermission('edit_details', 'Hosts', '')): ?>
                                            <li>
                                                <a ng-href="{{ linkForEditDetails() }}" class="a-clean">
                                                    <i class="fa fa-cog"></i> <?php echo __('Edit details'); ?>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if ($this->Acl->hasPermission('deactivate', 'Hosts', '')): ?>
                                            <li>
                                                <a ng-click="confirmDeactivate(getObjectsForDelete())"
                                                   class="a-clean pointer">
                                                    <i class="fa fa-plug"></i> <?php echo __('Disable'); ?>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if ($this->Acl->hasPermission('add', 'hostgroups', '')): ?>
                                            <li>
                                                <a ng-href="{{ linkForAddToHostgroup() }}" class="a-clean">
                                                    <i class="fa fa-sitemap"></i> <?php echo __('Add to hostgroup'); ?>
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <li>
                                            <a ng-href="{{ linkForPdf() }}" class="a-clean">
                                                <i class="fa fa-file-pdf-o"></i> <?php echo __('List as PDF'); ?>
                                            </a>
                                        </li>

                                        <?php if ($this->Acl->hasPermission('externalcommands', 'hosts')): ?>
                                            <li class="divider"></li>

                                            <li>
                                                <a href="javascript:void(0);"
                                                   ng-click="rescheduleHost(getObjectsForExternalCommand())">
                                                    <i class="fa fa-refresh"></i> <?php echo __('Reset check time'); ?>
                                                </a>
                                            </li>

                                            <li>
                                                <a href="javascript:void(0);"
                                                   ng-click="hostDowntime(getObjectsForExternalCommand())">
                                                    <i class="fa fa-clock-o"></i> <?php echo __('Set planned maintenance times'); ?>
                                                </a>
                                            </li>

                                            <li>
                                                <a href="javascript:void(0);"
                                                   ng-click="acknowledgeHost(getObjectsForExternalCommand())">
                                                    <i class="fa fa-user"></i> <?php echo __('Acknowledge host status'); ?>
                                                </a>
                                            </li>

                                            <li>
                                                <a href="javascript:void(0);"
                                                   ng-click="disableHostNotifications(getObjectsForExternalCommand())">
                                                    <i class="fa fa-envelope-o"></i> <?php echo __('Disable notifications'); ?>
                                                </a>
                                            </li>

                                            <li>
                                                <a href="javascript:void(0);"
                                                   ng-click="enableHostNotifications(getObjectsForExternalCommand())">
                                                    <i class="fa fa-envelope"></i> <?php echo __('Enable notifications'); ?>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <paginator paging="paging" click-action="changepage" ng-if="paging"></paginator>
                    </div>
                </div>
            </div>

            <reschedule-host></reschedule-host>
            <disable-host-notifications></disable-host-notifications>
            <enable-host-notifications></enable-host-notifications>
            <acknowledge-host author="<?php echo h($username); ?>"></acknowledge-host>
            <host-downtime author="<?php echo h($username); ?>"></host-downtime>
        </article>
    </div>
</section>

