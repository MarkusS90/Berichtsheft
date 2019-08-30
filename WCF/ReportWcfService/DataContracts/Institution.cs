using System;
using System.Runtime.Serialization;
using LinqToDB.Mapping;

namespace ReportWcfService
{
    [DataContract]
    [Table("institution")]
    public class Institution
    {
        [DataMember]
        [Column("id")]
        public Int32 Id { get; set; }

        [DataMember]
        [Column("name")]
        public String Name { get; set; }

        public Institution() { }
    }
}