import React, { useEffect, useState } from 'react';

function App() {
  const [rapports, setRapports] = useState([]);

  useEffect(() => {
    fetch('http://localhost:8000/api')
    .then(response => response.json())
    .then(rapports => {
      setRapports(rapports);
      console.log(rapports);
    });
  }, []);
  return (
    
    <div className="row">
      <div className="col-md-4">

      </div>
      <div className="col-md-8">
        <div className="table-responsive p-3">
          <fieldset className="border p-2">
            <legend>Les rapports d'activités</legend>
            <table className="table table-sm table-bordered table-striped text-center mb-0">
              <thead>
                <tr>
                  <th width="13%">Date de création</th>
                  <th width="10%">Installations</th>
                  <th width="10%">Inter-Qualités</th>
                  <th width="12%">Inter-Dépannages</th>
                  <th width="10%">Visites</th>
                  <th width="10%">Récuperations</th>
                  <th width="18%">Autres</th>
                  <th width="15%">Actions</th>
                </tr>
              </thead>
                    <tbody>
                        {rapports.map(rapport => (
                          <tr>
                            <td>{ rapport.createdAt.toString()}</td>
                            <td>{ rapport.installation }</td>
                            <td>{ rapport.interqualite }</td>
                            <td>{ rapport.interdepannage }</td>
                            <td>{ rapport.visite }</td>
                            <td>{ rapport.recuperation }</td>
                            <td>{ rapport.autre }</td>
                            <td>
                                <div className="btn-group" role="group">
                                    <a className="btn btn-sm btn-primary d-inline"
                                        href="">Modifier</a>
                                    
                                </div>


                            </td>
                        </tr>
                        ))}
                    </tbody>
            </table>
          </fieldset>


        </div>
      </div>
    </div>);
}

export default App;