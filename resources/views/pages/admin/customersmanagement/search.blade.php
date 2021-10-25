<table id="datatable" class="table table-bordered table-striped">
<tbody>
@foreach($customers as $customer)
                     	<tr>
                          <td>{{$customer->first_name}}</td>
                          <td>{{$customer->email}}</td>
                          <td>{{$customer->city}}</td>
			                    <td>
                            @if($customer->activated == 0)
                              Inactive
                            @else 
                              Active
                            @endif
                          </td>
                          <td>
                            {!! Form::open(array('url' => 'admin/user/' . $customer->id, 'class' => '', 'data-toggle' => 'tooltip')) !!}
                            {!! Form::hidden('_method', 'DELETE') !!}
                            {!! Form::button('<i class="fa fa-trash-o"></i>', array('class' => 'btn btn-danger btn-sm','type' => 'button', 'style' =>'width:auto; float:left;margin-right:5px;' ,'data-toggle' => 'modal', 'data-target' => '#confirmDelete', 'data-title' => 'Delete User', 'data-message' => 'Are you sure you want to delete this user ?')) !!}
                            {!! Form::close() !!}
                            <a class="btn btn-sm btn-info" href="{{ URL::to('/admin/user/' . $customer->id . '/edit') }}" data-toggle="tooltip">
                            <i class="fa fa-edit"></i>
                            </a>
                            @if($customer->activated == 1)
                            <a class="btn btn-sm btn-primary" href="{{route('unblock', ['id' => $customer->id])}}" data-toggle="tooltip">Inactive</a>
                            @else 
                            <a class="btn btn-sm btn-danger" href="{{route('block', ['id' => $customer->id])}}" data-toggle="tooltip">Active</a>
                            @endif
                          </td>
                      </tr>
                      @endforeach
                      </tbody>
                  </table>
                 {{ $customers->links() }}
